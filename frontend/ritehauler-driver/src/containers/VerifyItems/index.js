// @flow
import { connect } from "react-redux";
import { Actions } from "react-native-router-flux";
import React, { Component } from "react";
import {
  View,
  SafeAreaView,
  FlatList,
  Text,
  TouchableOpacity
} from "react-native";
import PropTypes from "prop-types";
import {
  FloatLabelTextInput,
  ImageView,
  BottomButton,
  SectionText,
  Separator,
  Loading,
  LoadingRequest
} from "../../components";
import OrderItem from "../OrderSummary/OrderItem";
import DeclineReason from "./DeclineReason";
import Modal from "../Modal";
import { Colors, Metrics, ApplicationStyles } from "../../theme";
import styles from "./styles";
import Utils from "../../util";
import {
  ENTITY_TYPE_ORDER_ITEM,
  ENTITY_TYPE_ID_UPDATE_ORDER_STATUS,
  USER_ENTITY_TYPE_ID,
  STATUS_ON_THE_WAY,
  STATUS_DECLINED
} from "../../constant";

// redux imports
import {
  selectCachedLoginUser,
  selectOrderItems
} from "../../reducers/reduxSelectors";
import { request } from "../../actions/OrderItemsActions";
import { request as updateAssignedOrderStatusRequest } from "../../actions/UpdateAssignedOrderStatus";

class VerifyItems extends Component {
  constructor(props) {
    super(props);
    this.onDeclineModalDonePress = this.onDeclineModalDonePress.bind(this);
    this.showHideModal = this.showHideModal.bind(this);
  }

  componentDidMount() {
    this.props.request({
      order_id: this.props.orderEntityId,
      entity_type_id: ENTITY_TYPE_ORDER_ITEM
    });
  }

  renderFooter() {
    return (
      <View style={styles.footerWrapper}>
        <Text style={[ApplicationStyles.tBold16, styles.basePadding]}>
          Extra Item
        </Text>
        <TouchableOpacity
          style={[
            {
              flex: 1
            },
            styles.basePadding
          ]}
          onPress={Actions.addItems}
        >
          <Text
            style={[
              ApplicationStyles.sb14Orange,
              { flex: 1, textAlign: "right" }
            ]}
          >
            Add Item
          </Text>
        </TouchableOpacity>
      </View>
    );
  }

  renderList() {
    return (
      <FlatList
        data={this.props.orderItems}
        contentContainerStyle={{ paddingHorizontal: Metrics.baseMargin }}
        bounces={false}
        renderItem={({ item }) => (
          <OrderItem
            title={item.title}
            dimensions={item.dimensions}
            quantity={item.quantity}
            dollar={item.dollar}
          />
        )}
        keyExtractor={item => `${item.title}_${item.quantity}`}
        ListHeaderComponent={() => <SectionText title="Items" />}
        ListFooterComponent={() => this.renderFooter()}
        ItemSeparatorComponent={() => (
          <View
            style={{
              paddingHorizontal: Metrics.baseMargin,
              backgroundColor: Colors.background.primary
            }}
          >
            <Separator />
          </View>
        )}
      />
    );
  }

  renderButton() {
    return (
      <View
        style={{
          flexDirection: "row",
          flex: 1,
          position: "absolute",
          bottom: 0
        }}
      >
        <BottomButton
          title="Verify"
          title2="Decline"
          onPress={this._onVerifyItems}
          onPress2={() => this.showHideModal(true)}
          style={{ flex: 1 }}
        />
      </View>
    );
  }

  _onVerifyItems = () => {
    this.props.updateAssignedOrderStatusRequest({
      entity_type_id: ENTITY_TYPE_ID_UPDATE_ORDER_STATUS,
      order_status: STATUS_ON_THE_WAY,
      order_id: this.props.orderID,
      driver_id: this.props.user.data.entity_id,
      login_entity_id: this.props.user.data.entity_id,
      login_entity_type_id: USER_ENTITY_TYPE_ID
    });
  };

  onDeclineModalDonePress(declineReason) {
    this.showHideModal(false);
    this.declineOrder(declineReason);
  }

  declineOrder = declineReason => {
    this.props.updateAssignedOrderStatusRequest({
      entity_type_id: ENTITY_TYPE_ID_UPDATE_ORDER_STATUS,
      order_status: STATUS_DECLINED,
      order_id: this.props.orderID,
      driver_id: this.props.user.data.entity_id,
      login_entity_id: this.props.user.data.entity_id,
      login_entity_type_id: USER_ENTITY_TYPE_ID,
      comment: declineReason
    });
  };

  showHideModal(visibility) {
    this.modal.setModalVisible(visibility);
  }

  render() {
    if (this.props.isFetching) {
      return <LoadingRequest />;
    }

    return (
      <SafeAreaView style={styles.safeAreaView}>
        <View style={styles.containerStyle}>
          <View style={{ marginBottom: Metrics.ratio(56) }}>
            {this.renderList()}
          </View>
          <View style={{ flex: 1 }}>{this.renderButton()}</View>
          <Modal ref={ref => (this.modal = ref)}>
            <DeclineReason cbOnDonePress={this.onDeclineModalDonePress} />
          </Modal>
          <Loading loading={this.props.updateAssignedOrderStatusFetching} />
        </View>
      </SafeAreaView>
    );
  }
}

const mapStateToProps = state => {
  const user = { ...state.user, data: selectCachedLoginUser(state.user.data) };
  return {
    isFetching: state.orderItems.isFetching,
    user: user,
    orderID: state.todaysOrders.data[0].entity_id,
    updateAssignedOrderStatusFetching:
      state.updateAssignedOrderStatus.isFetching,
    orderItems: selectOrderItems(state.orderItems.data),
    orderEntityId: state.todaysOrders.data[0].entity_id
  };
};

const actions = { request, updateAssignedOrderStatusRequest };

export default connect(mapStateToProps, actions)(VerifyItems);
