// @flow
import _ from "lodash";
import React, { Component } from "react";
import { Keyboard, View } from "react-native";
import { Actions } from "react-native-router-flux";
import { connect } from "react-redux";
import PropTypes from "prop-types";

import styles from "./styles";
import { Images, Strings } from "../../theme";
import { RightViewNavigation, EmptyView } from "../../appComponents";
import { FlatListWebServices } from "../../components";
import { Filter } from "../../models";
import Item from "./Item";

import { ENTITY_TYPE_ID_ORDER } from "../../config/WebService";
import {
  request as orderListingRequest,
  resetOrderListing
} from "../../actions/OrderListingActions";

class Orders extends Component {
  static propTypes = {
    user: PropTypes.object.isRequired,
    orderListing: PropTypes.object.isRequired,
    networkInfo: PropTypes.object.isRequired,
    orderListingRequest: PropTypes.func.isRequired,
    resetOrderListing: PropTypes.func.isRequired,
    notificationInfo: PropTypes.object.isRequired
  };

  static defaultProps = {};

  constructor(props) {
    super(props);
    this.openFilterScreen = this.openFilterScreen.bind(this);
    this.fetchData = this.fetchData.bind(this);
    this.filtersSelected = this.filtersSelected.bind(this);
    this.renderEmptyView = this.renderEmptyView.bind(this);
  }

  componentWillMount() {
    Keyboard.dismiss();
  }

  componentDidMount() {
    Actions.refresh({
      right: () => (
        <RightViewNavigation
          image={Images.filter}
          action={this.openFilterScreen}
          ref={ref => {
            this.filterButton = ref;
          }}
        />
      )
    });
    this.fetchData(true, 0, true);
  }

  componentWillReceiveProps(nextProps) {
    if (!_.isEqual(nextProps.notificationInfo, this.props.notificationInfo)) {
      this.fetchData(true, 0);
    }
  }

  componentWillUnmount() {
    this.props.resetOrderListing();
  }

  fetchData(reset, offset = 0, clearList: false) {
    const payload = {
      entity_type_id: ENTITY_TYPE_ID_ORDER,
      customer_id: this.props.user.entity_id,
      request_parameter:
        "pickup_date,pickup_time,order_status,payment_method_type,pre_grand_total,grand_total,card_type,transaction_id",
      hook: "order_pickup,order_dropoff",
      order_pickup_param: "address",
      order_dropoff_param: "address",
      detail_key: "order_status",
      offset,
      mobile_json: 1,
      ...this.filterOptions
    };
    this.props.orderListingRequest(payload, reset, clearList);
  }

  /*
  order_by:"updated_at",
  sorting:"desc"
  */

  filtersSelected(filterOptions, filtersCount) {
    this.filterButton.setBadgeCount(filtersCount);
    this.filterOptions = filterOptions;
    this.fetchData(true, 0, true);
  }

  openFilterScreen() {
    this.filterModal.show(this.filterOptions);
  }

  filterOptions = {};

  _renderList() {
    const { networkInfo, orderListing } = this.props;
    const {
      isFetching,
      isPullToRefresh,
      data,
      page,
      failure,
      errorMessage
    } = orderListing;
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
      />
    );
  }

  _renderFilterModal() {
    return (
      <Filter
        ref={ref => {
          this.filterModal = ref;
        }}
        onFiltersSelected={this.filtersSelected}
      />
    );
  }

  keyExtractor(item) {
    return `${item.entity_id}`;
  }

  renderItem({ item }) {
    return <Item data={item} />;
  }

  renderListSeparator() {
    return <View style={styles.separator} />;
  }

  renderEmptyView() {
    if (_.isEmpty(this.filterOptions, true)) {
      return (
        <EmptyView
          image={Images.emptyOrders}
          title={Strings.noOrderFound}
          description={Strings.noOrdersDescription}
          buttonText={Strings.placeOrder}
          onPressButton={() => Actions.popTo("consumerLocation")}
        />
      );
    }
    return (
      <EmptyView
        image={Images.emptyOrders}
        title={Strings.noOrderFound}
        description={Strings.noOrdersFilterDescription}
        buttonText={Strings.refresh}
        onPressButton={() => this.fetchData(true, 0, true)}
      />
    );
  }

  render() {
    return (
      <View style={styles.list}>
        {this._renderFilterModal()}
        {this._renderList()}
      </View>
    );
  }
}

const mapStateToProps = store => ({
  user: store.user.data,
  orderListing: store.orderListing,
  networkInfo: store.networkInfo,
  notificationInfo: store.notificationInfo
});
const actions = { orderListingRequest, resetOrderListing };

export default connect(
  mapStateToProps,
  actions
)(Orders);
