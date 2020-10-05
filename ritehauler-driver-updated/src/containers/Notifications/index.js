// @flow
import _ from "lodash";
import React, { Component } from "react";
import { Keyboard, View } from "react-native";
import moment from "moment";
import { connect } from "react-redux";
import { Actions } from "react-native-router-flux";
import PropTypes from "prop-types";
import { Text, FlatList, LoadingRequest } from "../../components";
import { ListEmpty } from "../../appComponents";
import { Strings, Images } from "../../theme";
import styles from "./styles";
import Item from "./Item";

import {
  USER_ENTITY_TYPE_ID,
  DATE_FORMAT,
  FLAT_LIST_ON_END_REACHED_THRESHOLD
} from "../../constant";

// redux imports
import {
  request as notificationListingRequest,
  clearNotificationListing
} from "../../actions/NotificationListingActions";
import { request as requestAssignedOrders } from "../../actions/AssignedOrders";
import { selectCachedLoginUser } from "../../reducers/reduxSelectors";

class Notifications extends Component {
  constructor(props) {
    super(props);
    this.cbOnPress = this.cbOnPress.bind(this);
    this.renderItem = this.renderItem.bind(this);
  }

  componentDidMount() {
    this.fetchData(true);
  }

  componentWillMount() {
    this.props.clearNotificationListing();
  }

  fetchData = (reset, offset = 0) => {
    const payload = {
      entity_type_id: USER_ENTITY_TYPE_ID,
      entity_id: this.props.user.entity_id,
      offset
    };
    this.props.notificationListingRequest(payload, reset);
  };

  keyExtractor(item) {
    return `${item.entity_history_id}`;
  }

  renderItem({ item, index }) {
    return (
      <Item
        data={item}
        cbOnPress={this.cbOnPress}
        index={index}
        dataLength={this.props.notificationListing.length}
      />
    );
  }

  cbOnPress() {
    this.props.requestAssignedOrders({
      driver_id: this.props.user.entity_id,
      pickup_date: moment().format(DATE_FORMAT),
      hook: "order_pickup,order_dropoff",
      detail_key: "customer_id"
    });
    Actions.pop();
  }

  renderListSeparator() {
    return <View style={styles.separator} />;
  }

  render() {
    if (
      this.props.notificationListing.isFetching &&
      !this.props.notificationListing.data.length
    ) {
      return <LoadingRequest />;
    }

    return (
      <FlatList
        style={styles.list}
        data={this.props.notificationListing.data}
        renderItem={this.renderItem}
        refreshing={this.props.notificationListing.isFetching}
        onRefresh={() => this.fetchData(true)}
        keyExtractor={item => this.keyExtractor(item)}
        ListEmptyComponent={() => (
          <ListEmpty
            title="You have no new notifications"
            description="We will let you know when we have got something new for you"
            cbOnRetry={this.fetchData}
            image={Images.emptyNotification}
          />
        )}
        onEndReachedThreshold={FLAT_LIST_ON_END_REACHED_THRESHOLD}
        onEndReached={() => {
          this.props.notificationListing.data.length > 9
            ? this.fetchData(
                false,
                this.props.notificationListing.page.next_offset
              )
            : {};
        }}
      />
    );
  }
}

const mapStateToProps = ({ user, notificationListing }) => ({
  user: selectCachedLoginUser(user.data),
  notificationListing: notificationListing
});

const actions = {
  notificationListingRequest,
  requestAssignedOrders,
  clearNotificationListing
};

export default connect(mapStateToProps, actions)(Notifications);
