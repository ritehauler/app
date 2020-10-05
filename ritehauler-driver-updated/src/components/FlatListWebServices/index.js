// @flow
import _ from "lodash";
import React from "react";
import PropTypes from "prop-types";
import { FlatList as FlatListRN, View } from "react-native";

import Utils from "../../util";
import {
  LoadingRequest,
  EmptyViewRequest,
  LoadingFooterRequest,
  NoInternetViewRequest,
  NoInternetViewBottomRequest
} from "../";

export default class FlatListWebServices extends React.Component {
  static propTypes = {
    data: PropTypes.array.isRequired,
    pageLimit: PropTypes.number.isRequired,
    emptyView: PropTypes.func,
    renderItem: PropTypes.func.isRequired,
    keyExtractor: PropTypes.func,
    isFetching: PropTypes.bool.isRequired,
    failure: PropTypes.bool.isRequired,
    isPullToRefresh: PropTypes.bool.isRequired,
    isInternetConnected: PropTypes.bool.isRequired,
    fetchData: PropTypes.func.isRequired,
    hideEmptyView: PropTypes.bool,
    emptyMessage: PropTypes.string,
    page: PropTypes.object
  };

  static defaultProps = {
    keyExtractor: Utils.keyExtractor,
    emptyView: null,
    hideEmptyView: false,
    page: {},
    emptyMessage: "No Records Found"
  };

  state = {
    isInternetConnected: this.props.isInternetConnected
  };

  componentWillReceiveProps(nextProps) {
    if (this.props.isInternetConnected && !nextProps.isInternetConnected) {
      this.state.isInternetConnected = false;
    }

    if (
      nextProps.isInternetConnected &&
      !this.props.isInternetConnected &&
      !this.isFirstTimeSync &&
      nextProps.data.length > 0
    ) {
      nextProps.fetchData(true);
    }

    if (
      !this.isFirstTimeSync &&
      !nextProps.isFetching &&
      this.props.isFetching &&
      !nextProps.failure
    ) {
      this.isFirstTimeSync = true;
    }

    if (
      !this.props.isFetching &&
      nextProps.isFetching &&
      nextProps.isInternetConnected
    ) {
      this.state.isInternetConnected = true;
    }
  }

  shouldComponentUpdate(nextProps, nextState) {
    return (
      !_.isEqual(nextProps, this.props) || !_.isEqual(nextState, this.state)
    );
  }

  isFirstTimeSync = false;

  _onRetryPress(reset = false) {
    if (this.props.isInternetConnected) {
      this.props.fetchData(reset, reset ? 0 : this.props.data.length);
    } else {
      Utils.showMessage("No Internet Connection Found", "error");
    }
  }

  _renderListFooter() {
    const { isInternetConnected } = this.state;
    const { data, isFetching, isPullToRefresh, pageLimit, page } = this.props;

    const checkPage =
      page && page.total_records && data.length < page.total_records;

    if ((checkPage || data.length % pageLimit === 0) && !isInternetConnected) {
      return (
        <NoInternetViewBottomRequest
          onRetryPress={() => this._onRetryPress()}
        />
      );
    }

    if (isFetching && !isPullToRefresh) {
      return <LoadingFooterRequest />;
    }

    return null;
  }

  getReference = () => {
    return this.flatListWebServices;
  };

  _onEndReach = () => {
    const {
      isFetching,
      isInternetConnected,
      data,
      pageLimit,
      fetchData,
      page
    } = this.props;
    const checkPage =
      page && page.total_records && data.length < page.total_records;

    if (
      !isFetching &&
      isInternetConnected &&
      data &&
      (checkPage || data.length % pageLimit === 0)
    ) {
      fetchData(false, data.length);
    }
  };

  _renderCustomEmptyView() {
    const { emptyView } = this.props;
    return <View style={{ flex: 1 }}>{emptyView()}</View>;
  }

  render() {
    const { isInternetConnected } = this.state;

    const {
      isFetching,
      isPullToRefresh,
      data,
      fetchData,
      emptyView,
      hideEmptyView,
      emptyMessage,
      failure
    } = this.props;
    const { ...rest } = this.props;

    if (isFetching && !data.length) {
      return <LoadingRequest />;
    }

    if ((!isInternetConnected || failure) && !data.length) {
      const message = !isInternetConnected
        ? "No internet connection. Make sure wi-fi or celluar data is turned on, then try again"
        : "Looks like something went wrong.";
      return (
        <NoInternetViewRequest
          message={message}
          onRetryPress={() => this._onRetryPress(true)}
        />
      );
    }
    if (!isFetching && !data.length) {
      if (hideEmptyView) {
        return null;
      }
      if (!emptyView) {
        return <EmptyViewRequest message={emptyMessage} />;
      }
      return this._renderCustomEmptyView();
    }

    return (
      <FlatListRN
        ref={ref => {
          this.flatListWebServices = ref;
        }}
        refreshing={isPullToRefresh}
        onEndReachedThreshold={0.1}
        onRefresh={() => fetchData(true)}
        onEndReached={this._onEndReach}
        ListFooterComponent={() => this._renderListFooter()}
        {...rest}
      />
    );
  }
}
