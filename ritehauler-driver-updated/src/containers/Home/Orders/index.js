// @flow
import _ from "lodash";
import { connect } from "react-redux";
import React, { Component } from "react";
import { View, SectionList } from "react-native";
import PropTypes from "prop-types";
import styles from "./styles";
import { INITIAL_NUMBER_TO_RENDER } from "../../../constant";
import {
  BottomButton,
  //  OrderItem,
  Spacer,
  Text,
  LoadingRequest,
  SectionText
} from "../../../components";
import { ListEmpty } from "../../../appComponents";
import { Actions } from "react-native-router-flux";
import OrderItem from "./OrderItem";
import { Images, Metrics, ApplicationStyles } from "../../../theme";
import { OrderItemHandler } from "./OrderItemHandler";
import { request as pendingOrdersRequest } from "../../../actions/PendingOrdersActions";
//import LoadingRequest from "../../components/LoadingRequest";

// redux imports
import {
  selectorLocation,
  selectCachedLoginUser,
  constructPendingSectionListData
} from "../../../reducers/reduxSelectors";

class Orders extends Component {
  constructor(props) {
    super(props);
    this.cbOnItemPress = this.cbOnItemPress.bind(this);
    this.cbOnRetry = this.cbOnRetry.bind(this);
  }

  componentDidMount() {
    this._fetchPendingOrders();
  }

  shouldComponentUpdate(nextProps, nextState) {
    return !_.isEqual(this.props.pendingOrders, nextProps.pendingOrders);
  }

  _fetchPendingOrders = () => {
    this.props.pendingOrdersRequest({
      driver_id: this.props.userID,
      //detail_key: "customer_id,truck_id,profession_id",
      hook: "order_pickup,order_dropoff"
    });
  };

  cbOnRetry() {
    this._fetchPendingOrders();
  }

  renderListSeparator() {
    return (
      <View style={styles.listSeparatorStyle}>
        <Spacer style={styles.spaceSeparatorStyle} />
      </View>
    );
  }

  handleOrderItemPress(index, sectionKey) {
    Actions.orderSummary();
  }

  renderItem = ({ item, section, index }) => {
    return (
      <OrderItem
        index={index}
        data={OrderItemHandler(item, this.props.location)}
        cbOnItemPress={this.cbOnItemPress}
      />
    );
  };

  cbOnItemPress(orderEntityID, orderID) {
    Actions.orderSummary({
      title: orderID && orderID.length ? orderID : "Order Summary",
      orderEntityID: orderEntityID
    });
  }

  renderSectionHeader = ({ section: item }) => {
    return <SectionText title={item.title} />;
  };

  renderList(data) {
    return (
      <SectionList
        refreshing={this.props.pendingOrders.isFetching}
        onRefresh={this._fetchPendingOrders}
        sections={data}
        renderItem={this.renderItem}
        initialNumToRender={INITIAL_NUMBER_TO_RENDER}
        renderSectionHeader={this.renderSectionHeader}
        keyExtractor={item => item.order_number}
        ItemSeparatorComponent={this.renderListSeparator}
        showsVerticalScrollIndicator={false}
        style={styles.list}
        contentContainerStyle={styles.listContainer}
        stickySectionHeadersEnabled={false}
        ListEmptyComponent={() => (
          <ListEmpty
            title="No orders found"
            description="We will let you know when we've orders for you"
          />
        )}
      />
    );
  }

  render() {
    const { pendingOrders } = this.props;

    if (pendingOrders.isFetching && !pendingOrders.data[0].data.length) {
      return <LoadingRequest />;
    }

    if (!pendingOrders.isFetching && !pendingOrders.data[0].data.length) {
      return (
        <View style={styles.emptyWrapper}>
          <ListEmpty
            cbOnRetry={this.cbOnRetry}
            title="No orders found"
            description="We will let you know when we've orders for you"
          />
        </View>
      );
    }

    return (
      <View style={styles.containerStyle}>
        {this.renderList(pendingOrders.data)}
      </View>
    );
  }
}

const mapStateToProps = ({ user, location, pendingOrders }) => {
  return {
    location: selectorLocation(location),
    pendingOrders: {
      ...pendingOrders,
      data: constructPendingSectionListData(pendingOrders.data)
    },
    userID: selectCachedLoginUser(user.data).entity_id
  };
};

const actions = { pendingOrdersRequest };

export default connect(mapStateToProps, actions)(Orders);
