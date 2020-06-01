// @flow
import React, { Component } from "react";
import { View, Keyboard } from "react-native";
import { Actions } from "react-native-router-flux";
import { connect } from "react-redux";
import PropTypes from "prop-types";
import stripe from "tipsi-stripe";

import {
  GradientButton,
  RightViewNavigation,
  EmptyView
} from "../../appComponents";
import { Loader, FlatListWebServices } from "../../components";
import styles from "./styles";
import { Strings, Images } from "../../theme";

import Item from "./Item";
import { Message } from "../../models";
import Util from "../../util";

import { request as chargeCardRequest } from "../../actions/ChargeCardActions";
import { updateOrderInfo } from "../../actions/OrderActions";
import {
  listingRequest,
  addCardRequest,
  deleteCardRequest,
  resetCardListing
} from "../../actions/CardActions";
import { STRIPE_PUBLISHABLE_KEY } from "../../constants";

class PaymentMethods extends Component {
  static propTypes = {
    isOrderDetail: PropTypes.bool,
    showButton: PropTypes.bool,
    orderId: PropTypes.number,
    chargeCardRequest: PropTypes.func.isRequired,
    resetCardListing: PropTypes.func.isRequired,
    listingRequest: PropTypes.func.isRequired,
    addCardRequest: PropTypes.func.isRequired,
    deleteCardRequest: PropTypes.func.isRequired,
    chargeCard: PropTypes.object.isRequired,
    selectedId: PropTypes.string,
    order_number: PropTypes.string,
    updateOrderInfo: PropTypes.func.isRequired,
    cards: PropTypes.object.isRequired,
    networkInfo: PropTypes.object.isRequired,
    user: PropTypes.object.isRequired,
    orderStatus: PropTypes.string
  };

  static defaultProps = {
    isOrderDetail: false,
    showButton: true,
    orderId: -1,
    selectedId: "",
    order_number: "",
    orderStatus: ""
  };

  constructor(props) {
    super(props);
    this.onPressButton = this.onPressButton.bind(this);
    this.onPressItem = this.onPressItem.bind(this);
    this.renderListSeparator = this.renderListSeparator.bind(this);
    this.onPressDeleteItem = this.onPressDeleteItem.bind(this);
    this.renderItem = this.renderItem.bind(this);
    this.keyExtractor = this.keyExtractor.bind(this);
    this.fetchData = this.fetchData.bind(this);
    this.renderEmptyView = this.renderEmptyView.bind(this);
    this.deleteCardAfterConfirm = this.deleteCardAfterConfirm.bind(this);
  }

  state = {
    selectedId: this.props.selectedId
  };

  componentWillMount() {
    Keyboard.dismiss();
  }

  componentDidMount() {
    Actions.refresh({
      right: () => (
        <RightViewNavigation text={Strings.add} action={this.handleAddCard} />
      )
    });
    stripe.setOptions({
      publishableKey: STRIPE_PUBLISHABLE_KEY
    });
    this.fetchData(true);
  }

  componentWillReceiveProps(nextProps) {
    // remove selected index if item removed which is selected
    if (
      !nextProps.cards.loading &&
      this.props.cards.loading &&
      !nextProps.cards.failureAddDelete &&
      this.requestType === "delete" &&
      this.selectedIndex === this.deleteIndex
    ) {
      this.selectedIndex = -1;
    }

    // if first time card added default select
    if (
      this.props.cards.data.length === 0 &&
      nextProps.cards.data.length === 1 &&
      this.requestType === "add" &&
      this.props.showButton
    ) {
      this.state.selectedId = nextProps.cards.data[0].id;
    }
  }

  componentWillUnmount() {
    this.props.resetCardListing();
  }

  onPressItem(item, index) {
    if (this.state.selectedId !== item.id) {
      this.setState({ selectedId: item.id });
    }
    this.selectedIndex = index;
  }

  onPressButton() {
    const { isOrderDetail, cards } = this.props;
    if (this.selectedIndex === -1) {
      Util.alert(Strings.errorMessageSelectCard);
    } else if (isOrderDetail) {
      this.sendChargeCardRequest();
    } else {
      const cardInfo = cards.data[this.selectedIndex];
      this.updateOrderSaveCard(cardInfo);
      Actions.orderSummary();
    }
  }

  onPressDeleteItem(index) {
    this.deleteIndex = index;
    const { currentScene } = Actions;
    if (this.deleteCardModel && currentScene === "paymentMethod") {
      if (this.props.cards.data.length === 1) {
        Util.alert(Strings.errorMessageCardDelete);
      } else {
        this.deleteCardModel.show();
      }
    }
  }

  updateOrderSaveCard(cardInfo) {
    const data = {
      card_id: cardInfo.id,
      card_type: cardInfo.brand,
      card_last_digit: cardInfo.last4
    };
    this.props.updateOrderInfo(data);
  }

  sendChargeCardRequest() {
    const cardInfo = this.props.cards.data[this.selectedIndex];
    const payload = {
      order_id: this.props.orderId,
      order_number: this.props.order_number,
      card_token: cardInfo.id,
      orderStatus: this.props.orderStatus,
      mobile_json: 1
    };
    this.props.chargeCardRequest(payload);
  }

  handleAddCard = async () => {
    try {
      const token = await stripe.paymentRequestWithCardForm();
      this.addCardRequest(token);
    } catch (error) {
      //console.log("error", error);
    }
  };

  selectedIndex = -1;
  deleteIndex = -1;
  requestType = "";

  addCardRequest = token => {
    this.requestType = "add";
    const payload = {
      card_token: token.tokenId,
      entity_auth_id: this.props.user.entity_auth_id,
      mobile_json: 1
    };
    this.props.addCardRequest(payload);
  };

  deleteCardAfterConfirm = () => {
    this.deleteCardModel.hide();
    setTimeout(() => {
      this.requestType = "delete";
      const cardDelete = this.props.cards.data[this.deleteIndex];
      const payload = {
        card_id: cardDelete.id,
        entity_auth_id: this.props.user.entity_auth_id,
        mobile_json: 1
      };
      this.props.deleteCardRequest(payload, this.deleteIndex);
    }, 500);
  };

  _renderLoading() {
    const { cards, chargeCard } = this.props;
    const isLoading = cards.loading || chargeCard.isFetching;
    return (
      <Loader
        ref={ref => {
          this.loader = ref;
        }}
        loading={isLoading}
      />
    );
  }

  fetchData(reset, offset = 0) {
    this.requestType = "list";
    const payload = {
      entity_auth_id: this.props.user.entity_auth_id,
      offset,
      mobile_json: 1
    };
    this.props.listingRequest(payload, reset);
  }

  _renderList() {
    const { networkInfo, cards } = this.props;
    const {
      isFetching,
      isPullToRefresh,
      data,
      page,
      failure,
      errorMessage
    } = cards;
    const isInternetConnected = networkInfo.isNetworkConnected;
    return (
      <FlatListWebServices
        data={data}
        page={page}
        emptyMessage={Strings.noOrderFound}
        isFetching={isFetching}
        errorMessage={errorMessage}
        failure={failure}
        isPullToRefresh={isPullToRefresh}
        isInternetConnected={isInternetConnected}
        renderItem={this.renderItem}
        keyExtractor={this.keyExtractor}
        fetchData={this.fetchData}
        contentContainerStyle={styles.contentContainerStyle}
        style={styles.list}
        ItemSeparatorComponent={this.renderListSeparator}
        showsVerticalScrollIndicator={false}
        emptyView={this.renderEmptyView}
        extraData={this.state.selectedId}
      />
    );
  }

  _renderButton() {
    const { showButton, isOrderDetail, cards } = this.props;
    if (showButton && cards.data.length > 0) {
      const buttonText = isOrderDetail
        ? Strings.initiateTransaction
        : Strings.continueButton;
      return (
        <GradientButton
          text={buttonText}
          ref={ref => {
            this.gradientButton = ref;
          }}
          onPress={this.onPressButton}
          setKeyboardEvent={false}
        />
      );
    }
    return null;
  }

  _confirmDeleteModal() {
    return (
      <Message
        ref={ref => {
          this.deleteCardModel = ref;
        }}
        description={Strings.deleteCardMessage}
        rightButtonTitle={Strings.yes}
        onPress={this.deleteCardAfterConfirm}
        leftButtonTitle={Strings.noThanks}
        isCancelable
      />
    );
  }

  keyExtractor(item) {
    return `${item.id}`;
  }

  renderItem({ item, index }) {
    const { selectedId } = this.state;
    const { showButton } = this.props;
    if (item.id === selectedId) {
      this.selectedIndex = index;
    }
    return (
      <Item
        data={item}
        isSelected={item.id === selectedId}
        onPress={this.onPressItem}
        onPressDelete={this.onPressDeleteItem}
        index={index}
        isSelectable={showButton}
      />
    );
  }

  renderListSeparator() {
    return <View style={styles.separatorList} />;
  }

  renderEmptyView() {
    const { showButton } = this.props;
    return (
      <EmptyView
        image={Images.emptyCards}
        title={Strings.noCardFound}
        description={
          showButton
            ? Strings.noPaymentDescriptionOrderPlace
            : Strings.noPaymentDescription
        }
      />
    );
  }

  render() {
    return (
      <View style={styles.container}>
        {this._renderList()}
        {this._renderButton()}
        {this._renderLoading()}
        {this._confirmDeleteModal()}
      </View>
    );
  }
}

const mapStateToProps = store => ({
  chargeCard: store.chargeCard,
  cards: store.cards,
  networkInfo: store.networkInfo,
  user: store.user.data
});
const actions = {
  chargeCardRequest,
  listingRequest,
  addCardRequest,
  deleteCardRequest,
  updateOrderInfo,
  resetCardListing
};

export default connect(
  mapStateToProps,
  actions
)(PaymentMethods);
