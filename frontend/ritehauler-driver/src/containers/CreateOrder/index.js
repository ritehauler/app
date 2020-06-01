// @flow
import React, { Component } from "react";
import {
  Text,
  View,
  Keyboard,
  SectionList,
  ScrollView,
  Alert
} from "react-native";
import { Actions } from "react-native-router-flux";
import { connect } from "react-redux";
import PropTypes from "prop-types";
import {
  Separator,
  BackHandler,
  BottomButton,
  Loading
} from "../../components";
import { resetOrderInfo } from "../../actions/OrderActions";
import {
  OrderItemsList,
  LeftViewNavigation,
  BoxError
} from "../../appComponents";
import styles from "./styles";
import { Metrics, Colors, Strings } from "../../theme";
import { Message } from "../../models";
import Modal from "../Modal";
import OrderVolumePopup from "./OrderVolumePopup";
import ConfirmOrderPopup from "./ConfirmOrderPopup";
import OrderItem from "../OrderSummary/OrderItem";
import DeclineReason from "../VerifyItems/DeclineReason";
import {
  WithKeyboardSubscription,
  WithKeyboardUnSubscription
} from "../HOC/WithKeyboardListener";
import Header from "./Header";
import Footer from "./Footer";
import {
  ENTITY_TYPE_ID_UPDATE_ORDER_STATUS,
  USER_ENTITY_TYPE_ID,
  STATUS_DECLINED,
  STATUS_ON_THE_WAY
} from "../../constant";
// redux imports
import {
  constructCreateOrderSectionList,
  selectOrderItems,
  selectCachedLoginUser,
  selectAddedExtraItems
} from "../../reducers/reduxSelectors";
import { request as addExtraItemRequest } from "../../actions/AddExtraItemsAction";
import {
  request as verifyVolumeRequest,
  clearVerifyVolume
} from "../../actions/VerifyVolumeActions";
import { request as updateAssignedOrderStatusRequest } from "../../actions/UpdateAssignedOrderStatus";
import { deleteItem as deleteExtraItem } from "../../actions/OrderActions";

class CreateOrder extends Component {
  constructor(props) {
    super(props);

    this.state = {
      isKeyboardVisible: false,
      verifyRequested: false,
      isVerified: false,
      isSuccessfulValidationPopupVisible: false
    };

    this.onPressItem = this.onPressItem.bind(this);
    this.onPressDelete = this.onPressDelete.bind(this);

    this.onLayoutChange = this.onLayoutChange.bind(this);
    this.handleBackPress = this.handleBackPress.bind(this);
    this.discardOrder = this.discardOrder.bind(this);
    this.onKeyboardVisible = this.onKeyboardVisible.bind(this);
    this.cbOnOrderItemPress = this.cbOnOrderItemPress.bind(this);
    this.cbOnItemDelete = this.cbOnItemDelete.bind(this);
    this.verifyVolume = this.verifyVolume.bind(this);
    this.addExtraItem = this.addExtraItem.bind(this);
    this.showHideOrderVolumePopup = this.showHideOrderVolumePopup.bind(this);
    this.onDeclineModalDonePress = this.onDeclineModalDonePress.bind(this);
    this.cbOnAddItemPress = this.cbOnAddItemPress.bind(this);
    this.updateAssignedOrderStatus = this.updateAssignedOrderStatus.bind(this);
    WithKeyboardSubscription(this, this.onKeyboardVisible);
  }

  componentWillUnmount() {
    WithKeyboardUnSubscription(this);
  }

  componentDidMount() {
    Actions.refresh({
      left: () => (
        <LeftViewNavigation text="Save" action={this.handleBackPress} />
      )
    });
  }

  onPressItem = (item, index) => {
    if (this.state.isVerified) {
      this.setState(
        {
          isVerified: false,
          verifyRequested: false
        },
        () => {
          this.props.clearVerifyVolume();
          Actions.addItems({ item, title: "Add Item", index });
        }
      );
    } else Actions.addItems({ item, title: "Add Item", index });
  };

  onPressDelete = index => {
    this.props.deleteItem(index);
    this.props.clearVerifyVolume();
  };

  onLayoutChange() {
    if (this.layoutChangeFirstTime) {
      this.layoutChangeFirstTime = false;
    } else {
      this.listItems.scrollToEnd();
    }
  }

  discardOrder() {
    this.discardModel.hide();
    this.props.resetOrderInfo();
    Actions.popTo("verifyItems");
  }

  layoutChangeFirstTime = true;

  onKeyboardVisible() {
    this.listItems.scrollToEnd();
  }

  _renderEmptyView() {
    return null;
    return (
      <BoxError
        ref={ref => {
          this.errorItems = ref;
        }}
        errorMessage={"No data found"}
        errorContainer={styles.errorContainer}
        containerStyle={styles.containerStyle}
      />
    );
  }

  _renderHeader() {
    return <Header />;
  }

  _renderItems() {
    return (
      <ScrollView
        style={styles.scroll}
        contentContainerStyle={styles.contentContainer}
        ref={ref => {
          this.listItems = ref;
        }}
      >
        <SectionList
          sections={this.props.sections}
          keyExtractor={(item, index) =>
            item.title ? `${item.title}_${index}` : `${item.item_name}_${index}`
          }
          renderItem={({ item, index, section }) => {
            return (
              <OrderItem
                title={item.title ? item.title : item.item_name}
                dimensions={
                  item.dimensions
                    ? item.dimensions
                    : `${item.width}x${item.height}x${item.length}, ${
                        item.weight
                      }kg`
                }
                quantity={item.quantity}
                dollar={
                  item.dollar ? item.dollar : item.is_expensive ? true : false
                }
                data={item}
                index={index}
                section={section}
                onPressItem={this.cbOnOrderItemPress}
                onPressDelete={() => {}}
                showSwipeOut={section.key === "addItems" ? false : true}
                cbOnDelete={this.cbOnItemDelete}
              />
            );
          }}
          renderSectionHeader={({ section: { title, key } }) => (
            <Header
              title={title}
              addItem={key === "addItems" ? Strings.addItem : undefined}
              cbOnAddItemPress={this.cbOnAddItemPress}
            />
          )}
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

        {this._renderFooter()}
      </ScrollView>
    );
  }

  cbOnAddItemPress() {
    this.setState(
      {
        verifyRequested: false,
        isVerified: false
      },
      () => {
        Actions.addItems();
      }
    );
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

  cbOnOrderItemPress(item, index, section) {
    if (section.key === "extraItems") {
      this.setState(
        {
          isVerified: false,
          verifyRequested: false
        },
        () => {
          this.props.clearVerifyVolume();
          Actions.addItems({ item, title: "Add Item", index });
        }
      );
    }
  }

  cbOnItemDelete(index) {
    this.props.deleteExtraItem(index);
  }

  _renderButton() {
    return !this.state.isKeyboardVisible ? (
      <View
        style={{
          flexDirection: "row",
          flex: 1,
          position: "absolute",
          bottom: 0
        }}
      >
        <BottomButton
          title={this.state.isVerified ? "Verify" : "Validate"}
          title2="Decline"
          onPress={() => {
            this.state.isVerified ? this.addExtraItem() : this.verifyVolume();
          }}
          onPress2={() => this.showHideModal(true)}
          style={{ flex: 1 }}
        />
      </View>
    ) : null;
  }

  showHideModal(visibility) {
    this.declineReasonModal.setModalVisible(visibility);
  }

  verifyVolume() {
    this.state.verifyRequested = true;
    this.props.verifyVolumeRequest({
      order_id: this.props.orderID,
      driver_id: this.props.user.entity_id,
      extra_item: this.props.extraItems
    });
  }

  addExtraItem() {
    if (this.props.extraItems.length) {
      this.props.addExtraItemRequest({
        order_id: this.props.orderID,
        driver_id: this.props.user.entity_id,
        extra_item: this.props.extraItems
      });
    } else {
      this.updateAssignedOrderStatus(STATUS_ON_THE_WAY, undefined, true);
      // Actions.home({
      //   type: "reset"
      // });
    }
  }

  _renderBackHandler() {
    return <BackHandler onBackPress={this.handleBackPress} />;
  }

  handleBackPress() {
    const { currentScene } = Actions;
    if (this.discardModel && currentScene === "createOrder") {
      this.discardModel.show();
      return true;
    }
    return false;
  }

  _renderDiscardModal() {
    return (
      <Message
        ref={ref => {
          this.discardModel = ref;
        }}
        description={"Are you sure to discard"}
        rightButtonTitle={"Yes"}
        onPress={this.discardOrder}
        leftButtonTitle={"No thanks"}
        isCancelable
      />
    );
  }

  _renderOrderVolumePopup() {
    return (
      <Modal ref={ref => (this.orderVolumePopup = ref)}>
        <OrderVolumePopup closeModal={this.showHideOrderVolumePopup} />
      </Modal>
    );
  }

  showHideOrderVolumePopup(value) {
    this.orderVolumePopup.setModalVisible(value);
  }

  _renderDeclineReasonPopup() {
    return (
      <Modal ref={ref => (this.declineReasonModal = ref)}>
        <DeclineReason cbOnDonePress={this.onDeclineModalDonePress} />
      </Modal>
    );
  }

  onDeclineModalDonePress(declineReason) {
    this.showHideModal(false);
    this.declineOrder(declineReason);
  }

  declineOrder = declineReason => {
    this.updateAssignedOrderStatus(STATUS_DECLINED, declineReason);
  };

  updateAssignedOrderStatus(status, declineReason = undefined, home = false) {
    let payload = {
      entity_type_id: ENTITY_TYPE_ID_UPDATE_ORDER_STATUS,
      order_status: status,
      order_id: this.props.orderID,
      driver_id: this.props.user.entity_id,
      login_entity_id: this.props.user.entity_id,
      login_entity_type_id: USER_ENTITY_TYPE_ID
    };
    if (declineReason) {
      payload["comment"] = declineReason;
    }
    if (home) {
      payload["home"] = true;
    }
    this.props.updateAssignedOrderStatusRequest(payload);
  }

  _renderConfirmOrderPopup() {
    return <ConfirmOrderPopup ref={ref => (this.confirmOrderPopup = ref)} />;
  }

  _renderLoadingModal() {
    if (!this.props.isFetching) return null;
    return <Loading loading={this.props.isFetching} />;
  }

  render() {
    return (
      <View style={styles.container}>
        {this._renderItems()}
        {this._renderButton()}
        {this._renderBackHandler()}
        {this._renderDiscardModal()}
        {this._renderOrderVolumePopup()}
        {this._renderDeclineReasonPopup()}
        {
          //  this._renderConfirmOrderPopup()
        }
        {this._renderLoadingModal()}
      </View>
    );
  }

  showVerificationSuccessful = () => {
    if (!this.state.isSuccessfulValidationPopupVisible) {
      this.state.isSuccessfulValidationPopupVisible = true;
      setTimeout(() => {
        Alert.alert(
          "Confirmation",
          "Extra item's successfully validated",
          [
            {
              text: "OK",
              onPress: () => {
                // clear verify volume reducer since its making this popup
                // visible if next extra item is added.
                this.state.isSuccessfulValidationPopupVisible = false;
                //              this.props.clearVerifyVolume();
              }
            }
          ],
          { cancelable: false }
        );
      }, 1000);
    }
  };

  componentDidUpdate(prevProps, prevState) {
    // checks if verification failed
    if (
      prevState.verifyRequested &&
      this.props.verifyVolume.failure &&
      !this.props.verifyVolume.isFetching
    ) {
      this.showHideOrderVolumePopup(true);
      this.setState({
        verifyRequested: false,
        isVerified: false
      });
    }

    //checks if verification successful
    if (
      prevState.verifyRequested &&
      this.props.verifyVolume.data.message === "Success" &&
      this.props.verifyVolume.data.error === 0 &&
      !this.props.verifyVolume.failure
    ) {
      this.props.clearVerifyVolume();
      this.setState(
        {
          isVerified: true,
          verifyRequested: false
        },
        this.showVerificationSuccessful
      );
    }

    if (prevProps.extraItems.length && !this.props.extraItems.length) {
      this.setState({
        isVerified: true
      });
    }
  }
}

const mapStateToProps = ({
  orderItems,
  orderInfo,
  todaysOrders,
  user,
  verifyVolume,
  addExtraItem,
  updateAssignedOrderStatus
}) => {
  return {
    isFetching:
      verifyVolume.isFetching ||
      addExtraItem.isFetching ||
      updateAssignedOrderStatus.isFetching,
    verifyVolume,
    sections: constructCreateOrderSectionList(
      selectOrderItems(orderItems.data),
      orderInfo.items
    ),
    modal: true,
    orderID: todaysOrders.data[0].entity_id,
    user: selectCachedLoginUser(user.data),
    extraItems: selectAddedExtraItems(orderInfo.items)
  };
};
const actions = {
  resetOrderInfo,
  addExtraItemRequest,
  verifyVolumeRequest,
  clearVerifyVolume,
  deleteExtraItem,
  updateAssignedOrderStatusRequest
};

export default connect(mapStateToProps, actions)(CreateOrder);
