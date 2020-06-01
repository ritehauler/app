// @flow
import _ from "lodash";
import React, { Component } from "react";
import { Keyboard, View } from "react-native";
import { connect } from "react-redux";
import PropTypes from "prop-types";

import { FlatListWebServices, Text } from "../../components";
import { EmptyView } from "../../appComponents";
import { Strings, Images } from "../../theme";
import styles from "./styles";
import Item from "./Item";

import { ENTITY_TYPE_ID_CUSTOMER } from "../../config/WebService";
import {
  request as notificationListingRequest,
  resetNotificationListing
} from "../../actions/NotificationListingActions";

class Notifications extends Component {
  static propTypes = {
    user: PropTypes.object.isRequired,
    notificationListing: PropTypes.object.isRequired,
    networkInfo: PropTypes.object.isRequired,
    notificationListingRequest: PropTypes.func.isRequired,
    resetNotificationListing: PropTypes.func.isRequired,
    notificationInfo: PropTypes.object.isRequired
  };

  static defaultProps = {};

  constructor(props) {
    super(props);
    this.fetchData = this.fetchData.bind(this);
    this.renderEmptyView = this.renderEmptyView.bind(this);
  }

  componentWillMount() {
    Keyboard.dismiss();
  }

  componentDidMount() {
    this.fetchData(true);
  }
  componentWillReceiveProps(nextProps) {
    if (!_.isEqual(nextProps.notificationInfo, this.props.notificationInfo)) {
      this.fetchData(true, 0);
    }
  }

  componentWillUnmount() {
    this.props.resetNotificationListing();
  }

  fetchData(reset, offset = 0, clearList: false) {
    const payload = {
      entity_type_id: ENTITY_TYPE_ID_CUSTOMER,
      entity_id: this.props.user.entity_id,
      offset,
      mobile_json: 1
    };
    this.props.notificationListingRequest(payload, reset, clearList);
    /*
    sorting: "desc",
    order_by: "created_at",
    */
  }

  keyExtractor(item) {
    return `${item.entity_history_id}`;
  }

  renderItem({ item }) {
    return <Item data={item} />;
  }

  renderListSeparator() {
    return <View style={styles.separator} />;
  }

  renderEmptyView() {
    return (
      <EmptyView
        image={Images.emptyNotification}
        title={Strings.noNotificationFound}
        description={Strings.noNotificationDescription}
        buttonText={Strings.refresh}
        onPressButton={() => this.fetchData(true, 0, true)}
      />
    );
  }

  render() {
    const { networkInfo, notificationListing } = this.props;
    const {
      isFetching,
      isPullToRefresh,
      data,
      page,
      failure,
      errorMessage
    } = notificationListing;
    const isInternetConnected = networkInfo.isNetworkConnected;
    return (
      <FlatListWebServices
        data={data}
        page={page}
        emptyMessage={Strings.noNotificationFound}
        isFetching={isFetching}
        errorMessage={errorMessage}
        failure={failure}
        isPullToRefresh={isPullToRefresh}
        isInternetConnected={isInternetConnected}
        renderItem={this.renderItem}
        keyExtractor={this.keyExtractor}
        fetchData={this.fetchData}
        style={styles.list}
        contentContainerStyle={styles.contentContainerStyle}
        ItemSeparatorComponent={this.renderListSeparator}
        showsVerticalScrollIndicator={false}
        emptyView={this.renderEmptyView}
      />
    );
  }
}

const mapStateToProps = store => ({
  user: store.user.data,
  notificationListing: store.notificationListing,
  notificationInfo: store.notificationInfo,
  networkInfo: store.networkInfo
});
const actions = { notificationListingRequest, resetNotificationListing };

export default connect(
  mapStateToProps,
  actions
)(Notifications);
