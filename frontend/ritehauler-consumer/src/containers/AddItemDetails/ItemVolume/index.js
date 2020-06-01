// @flow
import _ from "lodash";
import React, { Component } from "react";
import { View, Image, ActivityIndicator } from "react-native";
import PropTypes from "prop-types";
import { connect } from "react-redux";

import { Strings, Images, Colors } from "../../../theme";
import styles from "./styles";
import { Text, ButtonView, ImageServer } from "../../../components";
import Utils from "../../../util";

import { request as itemBoxRequest } from "../../../actions/ItemBoxActions";

class ItemVolume extends Component {
  static propTypes = {
    itemBox: PropTypes.object.isRequired,
    itemBoxRequest: PropTypes.func.isRequired,
    itemData: PropTypes.object,
    onItemVolumeChange: PropTypes.func.isRequired
  };

  static defaultProps = {
    itemData: {}
  };

  constructor(props) {
    super(props);
    this.retryCalculateVolume = this.retryCalculateVolume.bind(this);
  }

  state = {
    itemData: this.props.itemData
  };

  componentWillReceiveProps(nextProps) {
    if (!_.isEqual(nextProps.itemBox.data, this.state.itemData)) {
      this.setState({ itemData: nextProps.itemBox.data });
      this.props.onItemVolumeChange(nextProps.itemBox.data);
    }
  }

  shouldComponentUpdate(nextProps: Object, nextState: Object) {
    return (
      !_.isEqual(nextProps, this.props) || !_.isEqual(nextState, this.state)
    );
  }

  getItemBox = () => this.state.itemData;

  setStateEmpty = () => {
    if (!_.isEmpty(this.state.itemData, true)) {
      this.setState({ itemData: {} });
    }
  };

  sendRequest = (width, height, length) => {
    // set values local
    this.width = width;
    this.height = height;
    this.length = length;

    // set payload
    const payload = {
      width,
      height,
      length,
      mobile_json: 1
    };

    // send request
    this.props.itemBoxRequest(payload);
  };

  retryCalculateVolume() {
    this.sendRequest(this.width, this.height, this.length);
  }

  width = 0;
  height = 0;
  length = 0;

  _renderEmptyView() {
    const emptyText = !_.isEmpty(this.state.itemData, true)
      ? Strings.infoNoBox
      : Strings.infoToAddItemDetails;

    return (
      <View style={styles.emptyView}>
        <Image source={Images.cartonPlaceholder} />
        <Text size="xSmall" textAlign="center" style={styles.info}>
          {emptyText}
        </Text>
      </View>
    );
  }

  _renderErrorView() {
    const { errorMessage } = this.props.itemBox;
    return (
      <ButtonView style={styles.emptyView} onPress={this.retryCalculateVolume}>
        <Image source={Images.retryUpload} />
        <Text size="xSmall" textAlign="center" style={styles.info}>
          {errorMessage}
        </Text>
      </ButtonView>
    );
  }

  _renderVolumeView() {
    const { volume, gallery } = this.state.itemData;
    return (
      <View>
        <Text size="xxxSmall" color="secondary">
          {Strings.itemVolumeUnit}
        </Text>
        <Text style={styles.volume} size="small">
          {volume}
        </Text>
        <View style={styles.cartonImage}>
          <ImageServer
            style={styles.image}
            source={{
              uri: Utils.getImageFromGallery(gallery)
            }}
          />
        </View>
      </View>
    );
  }

  _renderLoadingView() {
    return (
      <View style={styles.loadingView}>
        <ActivityIndicator animating size="large" color={Colors.accent} />
      </View>
    );
  }

  render() {
    // get data from props
    const { isFetching, failure } = this.props.itemBox;
    const { itemData } = this.state;

    // set variables
    const hasData = !_.isEmpty(itemData, true) && itemData.entity_id;
    const showEmptyView = !hasData && !isFetching && !failure;
    const showVolumeView = hasData && !isFetching && !failure;

    // render view
    return (
      <View style={styles.container}>
        {isFetching && this._renderLoadingView()}
        {failure && this._renderErrorView()}
        {showEmptyView && this._renderEmptyView()}
        {showVolumeView && this._renderVolumeView()}
      </View>
    );
  }
}

const mapStateToProps = store => ({
  itemBox: store.itemBox
});
const actions = { itemBoxRequest };

export default connect(
  mapStateToProps,
  actions,
  null,
  { withRef: true }
)(ItemVolume);
