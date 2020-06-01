// @flow
import _ from "lodash";
import React from "react";
import PropTypes from "prop-types";
import { FlatList as FlatListRN, View } from "react-native";

import {
  Separator,
  LoadingRequest,
  EmptyViewRequest,
  LoadingFooterRequest,
  NoInternetViewRequest,
  NoInternetViewBottomRequest
} from "../";
import { Strings } from "../../theme";
import { EmptyView } from "../../appComponents";
import Util from "../../util";
import styles from "./styles";

export default class FlatListWebServices extends React.Component {
  static propTypes = {
    data: PropTypes.array.isRequired,
    page: PropTypes.object.isRequired,
    emptyView: PropTypes.func,
    renderItem: PropTypes.func.isRequired,
    keyExtractor: PropTypes.func,
    failure: PropTypes.bool,
    errorMessage: PropTypes.string,
    ItemSeparatorComponent: PropTypes.func,
    isFetching: PropTypes.bool.isRequired,
    isPullToRefresh: PropTypes.bool.isRequired,
    isInternetConnected: PropTypes.bool.isRequired,
    fetchData: PropTypes.func.isRequired,
    hideEmptyView: PropTypes.bool,
    emptyMessage: PropTypes.string
  };

  static defaultProps = {
    keyExtractor: Util.keyExtractor,
    ItemSeparatorComponent: () => <Separator />,
    emptyView: null,
    failure: false,
    errorMessage: "",
    hideEmptyView: false,
    emptyMessage: Strings.noRecordFound
  };

  state = {
    isInternetConnected: this.props.isInternetConnected
  };

  componentWillReceiveProps(nextProps) {
    if (this.props.isInternetConnected && !nextProps.isInternetConnected) {
      this.state.isInternetConnected = false;
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

  _onRetryPress(reset = false) {
    if (this.props.isInternetConnected) {
      this.props.fetchData(reset, reset ? 0 : this.props.data.length);
    } else {
      Util.alert(Strings.noInternetMessage, "error");
    }
  }

  _renderListFooter() {
    const { isInternetConnected } = this.state;
    const { data, isFetching, isPullToRefresh, page } = this.props;

    if (
      page &&
      page.total_records &&
      data.length < page.total_records &&
      !isInternetConnected
    ) {
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

  _onEndReach = () => {
    const {
      isFetching,
      isInternetConnected,
      data,
      page,
      fetchData
    } = this.props;
    if (
      !isFetching &&
      isInternetConnected &&
      page &&
      data &&
      page.total_records &&
      data.length < page.total_records
    ) {
      if (page.next_offset) {
        fetchData(false, page.next_offset);
      } else {
        fetchData(false, data.length);
      }
    }
  };

  _renderCustomEmptyView() {
    const { emptyView } = this.props;
    return <View style={styles.container}>{emptyView()}</View>;
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
      const message = this.props.errorMessage;
      return (
        <EmptyView
          title={message}
          description={Strings.noInternetFullMessage}
          buttonText={Strings.retry}
          onPressButton={() => this._onRetryPress(true)}
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
        refreshing={isPullToRefresh}
        onEndReached={this._onEndReach}
        onEndReachedThreshold={0.1}
        onRefresh={() => fetchData(true)}
        ListFooterComponent={() => this._renderListFooter()}
        {...rest}
      />
    );
  }
}
