// @flow
import _ from "lodash";
import React, { Component } from "react";
import PropTypes from "prop-types";
import { View } from "react-native";

import Util from "../../util";
import { Strings } from "../../theme";
import { LoadingRequest, EmptyViewRequest, NoInternetViewRequest } from "../";
import { EmptyView } from "../../appComponents";

export default class ServerRequestPage extends Component {
  static propTypes = {
    data: PropTypes.any.isRequired,
    emptyView: PropTypes.func,
    renderView: PropTypes.func.isRequired,
    isFetching: PropTypes.bool.isRequired,
    isInternetConnected: PropTypes.bool.isRequired,
    failure: PropTypes.bool,
    fetchData: PropTypes.func.isRequired,
    hideEmptyView: PropTypes.bool,
    emptyMessage: PropTypes.string,
    errorMessage: PropTypes.string,
    customStyle: PropTypes.oneOfType([
      PropTypes.array,
      PropTypes.object,
      PropTypes.number
    ])
  };

  static defaultProps = {
    emptyView: null,
    hideEmptyView: false,
    failure: false,
    errorMessage: "",
    emptyMessage: Strings.noRecordFound,
    customStyle: {}
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

  _onRetryPress() {
    if (this.props.isInternetConnected) {
      this.props.fetchData();
    } else {
      Util.alert(Strings.noInternetMessage);
    }
  }

  _renderCustomEmptyView() {
    const { emptyView } = this.props;
    return <View style={{ flex: 1 }}>{emptyView()}</View>;
  }

  _renderView() {
    const { renderView } = this.props;
    return renderView();
  }

  render() {
    const { isInternetConnected } = this.state;
    const {
      isFetching,
      data,
      emptyView,
      hideEmptyView,
      failure,
      emptyMessage,
      customStyle
    } = this.props;
    const dataIsEmpty = _.isEmpty(data, true);
    if (isFetching && dataIsEmpty) {
      return <LoadingRequest style={customStyle} />;
    }

    if ((!isInternetConnected || failure) && dataIsEmpty) {
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
    if (!isFetching && dataIsEmpty) {
      if (hideEmptyView) {
        return null;
      }
      if (!emptyView) {
        return <EmptyViewRequest message={emptyMessage} style={customStyle} />;
      }
      return this._renderCustomEmptyView();
    }

    return this._renderView();
  }
}
