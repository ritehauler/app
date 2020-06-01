// @flow
import React, { Component } from "react";
import { View, Keyboard, ScrollView } from "react-native";
import { Actions } from "react-native-router-flux";
import { connect } from "react-redux";
import PropTypes from "prop-types";

import { BackHandler } from "../../components";
import {
  GradientButton,
  OrderItemsList,
  LeftViewNavigation,
  BoxError
} from "../../appComponents";
import styles from "./styles";
import DataHandler from "../../util/DataHandler";
import { Strings } from "../../theme";
import { Message } from "../../models";

import Header from "./Header";
import Footer from "./Footer";

import {
  deleteItem,
  updateOrderInfo,
  resetOrderInfo
} from "../../actions/OrderActions";

class CreateOrder extends Component {
  static propTypes = {
    orderItems: PropTypes.array.isRequired,
    deleteItem: PropTypes.func.isRequired,
    resetOrderInfo: PropTypes.func.isRequired,
    updateOrderInfo: PropTypes.func.isRequired
  };

  static defaultProps = {};

  constructor(props) {
    super(props);
    this.onPressItem = this.onPressItem.bind(this);
    this.onPressDelete = this.onPressDelete.bind(this);
    this.onPressButton = this.onPressButton.bind(this);
    this.onLayoutChange = this.onLayoutChange.bind(this);
    this.handleBackPress = this.handleBackPress.bind(this);
    this.discardOrder = this.discardOrder.bind(this);
  }

  componentWillMount() {
    Keyboard.dismiss();
  }

  componentDidMount() {
    Actions.refresh({
      left: () => <LeftViewNavigation action={this.handleBackPress} />
    });
  }

  onPressItem = (item, index) => {
    Actions.addItem({ item, title: Strings.editItem, index });
    DataHandler.setFirstItem(false);
  };

  onPressDelete = index => {
    this.props.deleteItem(index);
  };

  onPressButton() {
    // get data time ,date,note and show errors
    const data = this.footer.getData();

    // get item length
    const orderItemsLength = this.props.orderItems.length;

    // check items greater than 0
    if (orderItemsLength === 0) {
      this.errorItems.setShowError(true);
    }

    // get is valid
    const isValid =
      orderItemsLength > 0 &&
      data.pickup_time !== "" &&
      data.pickup_date !== "";

    // if valid send to truck screen
    if (isValid) {
      this.props.updateOrderInfo(data);
      Actions.selectVehicle();
    }
  }

  onLayoutChange() {
    if (this.layoutChangeFirstTime) {
      this.layoutChangeFirstTime = false;
    } else {
      this.listItems.scrollToEnd();
    }
  }

  handleBackPress() {
    const { currentScene } = Actions;
    if (this.discardModel && currentScene === "createOrder") {
      this.discardModel.show();
      return true;
    }
    return false;
  }

  discardOrder() {
    setTimeout(() => {
      this.discardModel.hide();
      this.props.resetOrderInfo();
      Actions.popTo("consumerLocation");
      DataHandler.callBackOrderDiscard();
    }, 0);
  }

  layoutChangeFirstTime = true;

  _renderHeader() {
    return <Header />;
  }

  _renderFooter() {
    return (
      <Footer
        onLayoutChange={this.onLayoutChange}
        ref={ref => {
          this.footer = ref;
        }}
      />
    );
  }

  _renderEmptyView() {
    return (
      <BoxError
        ref={ref => {
          this.errorItems = ref;
        }}
        errorMessage={Strings.errorMessageItems}
        errorContainer={styles.errorContainer}
        containerStyle={styles.containerStyle}
      />
    );
  }

  _renderItems() {
    const { orderItems } = this.props;

    return (
      <ScrollView
        style={styles.scroll}
        ref={ref => {
          this.listItems = ref;
        }}
      >
        <OrderItemsList
          showSwipeOut
          data={orderItems}
          onPressItem={this.onPressItem}
          onPressDelete={this.onPressDelete}
          ListHeaderComponent={this._renderHeader()}
          customContainerStyle={styles.customContainerStyle}
          ListEmptyComponent={this._renderEmptyView()}
        />
        {this._renderFooter()}
      </ScrollView>
    );
  }

  _renderButton() {
    return (
      <GradientButton
        ref={ref => {
          this.gradientButton = ref;
        }}
        onPress={this.onPressButton}
      />
    );
  }

  _renderDiscardModal() {
    return (
      <Message
        ref={ref => {
          this.discardModel = ref;
        }}
        description={Strings.discardOrder}
        rightButtonTitle={Strings.yes}
        onPress={this.discardOrder}
        leftButtonTitle={Strings.noThanks}
        isCancelable
      />
    );
  }

  _renderBackHandler() {
    return <BackHandler onBackPress={this.handleBackPress} />;
  }

  render() {
    return (
      <View style={styles.container}>
        {this._renderItems()}
        {this._renderButton()}
        {this._renderBackHandler()}
        {this._renderDiscardModal()}
      </View>
    );
  }
}

const mapStateToProps = store => ({
  orderItems: store.orderInfo.items
});
const actions = { deleteItem, updateOrderInfo, resetOrderInfo };

export default connect(
  mapStateToProps,
  actions
)(CreateOrder);
