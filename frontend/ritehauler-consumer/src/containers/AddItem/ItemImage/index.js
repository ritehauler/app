// @flow
import _ from "lodash";
import React, { Component } from "react";
import { View, Image } from "react-native";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import ProgressBarAnimated from "react-native-progress-bar-animated";

import { Images, Metrics, Colors } from "../../../theme";
import styles from "./styles";
import { ButtonView } from "../../../components";
import Utils from "../../../util";

import { request as attachmentRequest } from "../../../actions/AttachmentActions";

class ItemImage extends Component {
  static propTypes = {
    file: PropTypes.any.isRequired,
    index: PropTypes.number.isRequired,
    attachmentRequest: PropTypes.func.isRequired,
    attachment: PropTypes.object,
    updateAttachment: PropTypes.object.isRequired,
    onRemovePicture: PropTypes.func.isRequired,
    onUploadSuccess: PropTypes.func.isRequired,
    onUploadFailure: PropTypes.func.isRequired
  };

  static defaultProps = { attachment: undefined };

  constructor(props) {
    super(props);
    this._onDeleteButtonClick = this._onDeleteButtonClick.bind(this);
    this._onPressRetry = this._onPressRetry.bind(this);
  }

  state = {
    attachment: this.props.attachment,
    itemId: Utils.getRandomNumber(),
    progress: 0,
    failure: false
  };

  componentDidMount() {
    if (this.props.attachment === undefined) {
      this._uploadAttachmentRequest();
    }
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.updateAttachment.itemId === this.state.itemId) {
      const { failure, progress, attachment } = nextProps.updateAttachment;
      this.setState({ attachment, failure, progress });
      if (attachment) {
        this.props.onUploadSuccess(this.props.index, attachment);
      } else if (failure) {
        this.props.onUploadFailure(this.props.index);
      }
    }
  }

  shouldComponentUpdate(nextProps) {
    return (
      _.isEqual(nextProps.updateAttachment.itemId, this.state.itemId) ||
      !_.isEqual(nextProps.file, this.props.file)
    );
  }

  _onPressRetry() {
    this._uploadAttachmentRequest();
  }

  _onDeleteButtonClick() {
    const { onRemovePicture, index } = this.props;
    onRemovePicture(index);
  }

  _uploadAttachmentRequest() {
    const { file } = this.props;
    const payload = {
      file,
      itemId: this.state.itemId
    };
    this.props.attachmentRequest(payload);
  }

  _renderImage() {
    const { file } = this.props;
    return (
      <Image
        source={{
          uri: file
        }}
        style={styles.image}
        resizeMode="cover"
      />
    );
  }

  _renderDeleteIcon() {
    return (
      <ButtonView
        onPress={this._onDeleteButtonClick}
        style={styles.cross}
        disableRipple
      >
        <Image source={Images.deleteImage} />
      </ButtonView>
    );
  }

  _renderProgressBar() {
    const { progress } = this.state;
    if (progress > 0) {
      return (
        <View style={styles.viewProgressBar}>
          <ProgressBarAnimated
            backgroundColor={Colors.accent}
            borderRadius={0}
            borderColor={Colors.background.transparent}
            width={Metrics.uploadImage}
            maxValue={100}
            height={10}
            value={progress}
          />
        </View>
      );
    }
    return null;
  }

  _renderRetry() {
    return (
      <ButtonView
        onPress={this._onPressRetry}
        style={styles.retry}
        disableRipple
      >
        <Image source={Images.retryUpload} />
      </ButtonView>
    );
  }

  render() {
    const { attachment, failure } = this.state;
    const showLoader = !failure && !attachment;
    return (
      <View>
        <View style={styles.imageContainer}>
          {this._renderImage()}
          {failure && this._renderRetry()}
          {showLoader && this._renderProgressBar()}
        </View>
        {attachment && this._renderDeleteIcon()}
      </View>
    );
  }
}

const mapStateToProps = store => ({
  updateAttachment: store.updateAttachment
});
const actions = { attachmentRequest };

export default connect(
  mapStateToProps,
  actions
)(ItemImage);
