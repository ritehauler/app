import React, { Component } from "react";
import { connect } from "react-redux";
import { Actions } from "react-native-router-flux";
import { View, FlatList, Picker } from "react-native";

import { Colors, Metrics, ApplicationStyles } from "../../theme";
import Modal from "../Modal";
import AnimatedModal from "../AnimatedModal";
import OrderFilter from "../OrderFilter";
import {
  //TabButtonLeft,
  LoadingRequest,
  LoadingFooterRequest
} from "../../components";
import { ListEmpty, FilterIcon as TabButtonLeft } from "../../appComponents";
import {
  ENTITY_TYPE_ID_MY_ORDERS,
  INITIAL_NUMBER_TO_RENDER,
  FLAT_LIST_ON_END_REACHED_THRESHOLD
} from "../../constant";
import MyOrderItem from "./MyOrderItem";
import { OrderItemHandler } from "./OrderItemHandler";
import WithLoader from "../HOC/WithLoader";
import Utils from "../../util";

// redux imports
import { request } from "../../actions/MyOrders";
import { request as orderStatusesRequest } from "../../actions/OrderStatusesActions";
import {
  selectCachedLoginUser,
  selectorLocation
} from "../../reducers/reduxSelectors";

class MyOrders extends Component<{}> {
  constructor(props) {
    super(props);

    this.state = {
      startDate: undefined,
      endDate: undefined,
      minAmount: undefined,
      maxAmount: undefined,
      orderID: undefined,
      orderStatus: undefined,
      isFilterActive: false,
      selectedStatusIndex: undefined,
      isPullToRefresh: false
    };

    this.handleFilterPress = this.handleFilterPress.bind(this);
    this.cbOnClear = this.cbOnClear.bind(this);
    this.cbOnDone = this.cbOnDone.bind(this);
    this.requestData = this.requestData.bind(this);
    this.cbOnRetry = this.cbOnRetry.bind(this);
  }
  componentDidMount() {
    Actions.refresh({
      right: () => (
        <TabButtonLeft
          ref={reference => (this.filterIcon = reference)}
          imagesArray={["filter"]}
          actions={[() => this.handleFilterPress(true)]}
        />
      )
    });
    if (!this.props.data.length) {
      this.requestData();
    }

    this.props.orderStatusesRequest({});
  }

  requestData(offset = undefined, isConcat = false) {
    this.props.request({
      driver_id: this.props.user.entity_id,
      hook: "order_pickup,order_dropoff",
      offset,
      isConcat,
      start_date: this.state.startDate
        ? Utils.getGMTDate(`${this.state.startDate} 00:00:00`)
        : undefined,
      end_date: this.state.endDate
        ? Utils.getGMTDate(`${this.state.endDate} 23:59:59`)
        : undefined,
      start_amount: this.state.minAmount,
      end_amount: this.state.maxAmount,
      order_status: this.state.orderStatus,
      order_number: this.state.orderID
    });
  }

  handleFilterPress(value) {
    if (this.modal) {
      this.modal.setModalVisible(value);
    }
    if (this.state.isFilterActive) {
      setTimeout(() => {
        this.orderFilter.setOrderFilter(
          this.state.startDate,
          this.state.endDate,
          this.state.minAmount,
          this.state.maxAmount,
          this.state.orderID,
          this.state.selectedStatusIndex
        );
      }, 500);
    }
  }

  renderOrderFilterModal() {
    return (
      <Modal ref={ref => (this.modal = ref)}>
        <OrderFilter
          ref={reference => {
            if (reference) {
              this.orderFilter = reference.getWrappedInstance();
            }
          }}
          closeModal={this.handleFilterPress}
          cbOnDone={this.cbOnDone}
          cbOnClear={this.cbOnClear}
        />
      </Modal>
    );
    // return (
    //   <Modal ref={ref => (this.modal = ref)}>
    //     <OrderFilter
    //       ref={reference => {
    //         if (reference) {
    //           this.orderFilter = reference.getWrappedInstance();
    //         }
    //       }}
    //       closeModal={this.handleFilterPress}
    //       cbOnDone={this.cbOnDone}
    //       cbOnClear={this.cbOnClear}
    //     />
    //   </Modal>
    // );
  }

  cbOnDone(
    startDate,
    endDate,
    minAmount,
    maxAmount,
    orderID,
    orderStatus,
    selectedStatusIndex,
    activeFilters = 1
  ) {
    this.setState(
      {
        startDate,
        endDate,
        minAmount,
        maxAmount,
        orderID,
        orderStatus,
        selectedStatusIndex,
        isFilterActive: true
      },
      () => {
        this.requestData();
        this.filterIcon.setShowIndicator(true, activeFilters);
      }
    );
  }

  cbOnClear() {
    this.setState(
      {
        startDate: undefined,
        endDate: undefined,
        minAmount: undefined,
        maxAmount: undefined,
        orderID: undefined,
        orderStatus: undefined,
        selectedStatusIndex: undefined,
        isFilterActive: false
      },
      () => this.requestData()
    );
    this.modal.setModalVisible(false);
    this.filterIcon.setShowIndicator(false);
  }

  render() {
    if (
      this.props.isFetching &&
      !this.props.data.length &&
      !this.state.isPullToRefresh
    ) {
      return <LoadingRequest />;
    }

    return (
      <View
        style={{
          flex: 1,
          backgroundColor: Colors.background.login
        }}
      >
        <FlatList
          style={{ margin: Metrics.baseMargin }}
          data={this.props.data}
          refreshing={this.props.isFetching && this.state.isPullToRefresh}
          onRefresh={() => {
            this.state.isPullToRefresh = true;
            this.requestData();
          }}
          renderItem={({ item, index }) => {
            return <MyOrderItem data={OrderItemHandler(item)} index={index} />;
          }}
          initialNumToRender={INITIAL_NUMBER_TO_RENDER}
          keyExtractor={item => item.created_at}
          ItemSeparatorComponent={() => (
            <View
              style={{
                backgroundColor: Colors.background.login,
                height: Metrics.baseMargin
              }}
            />
          )}
          onEndReached={() => {
            this.props.data.length > 9
              ? this.requestData(this.props.nextOffset, true)
              : {};
          }}
          onEndReachedThreshold={FLAT_LIST_ON_END_REACHED_THRESHOLD}
          showsVerticalScrollIndicator={false}
          ListEmptyComponent={() => (
            <ListEmpty
              cbOnRetry={this.cbOnRetry}
              title="No orders"
              description="No results found, try another search."
            />
          )}
          ListFooterComponent={() =>
            this.props.isFetching && !this.state.isPullToRefresh ? (
              <LoadingFooterRequest />
            ) : null
          }
        />
        {this.renderOrderFilterModal()}
      </View>
    );
  }

  cbOnRetry() {
    this.requestData();
  }

  componentDidUpdate(prevProps, prevState) {
    if (
      prevProps.isFetching &&
      !this.props.isFetching &&
      this.state.isPullToRefresh
    ) {
      this.setState({
        isPullToRefresh: false
      });
    }
  }
}

const mapStateToProps = ({ user, myOrders }) => {
  return {
    user: selectCachedLoginUser(user.data),
    ...myOrders
  };
};
const actions = { request, orderStatusesRequest };

export default connect(mapStateToProps, actions)(MyOrders);
