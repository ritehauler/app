// @flow
import _ from "lodash";
import React from "react";
import { View, Image } from "react-native";
import PropTypes from "prop-types";

import { Images, Strings } from "../../../theme";
import styles from "./styles";
import { Text, ButtonView } from "../../../components";
import { BoxError } from "../../../appComponents";
import MediaPicker from "../../../util/MediaPicker";
import Util from "../../../util";

import ItemImage from "../ItemImage";

class AddImages extends React.PureComponent {
  static propTypes = {
    title: PropTypes.string.isRequired,
    maxImages: PropTypes.number,
    imagesArray: PropTypes.array
  };

  static defaultProps = { maxImages: 3, imagesArray: [] };

  constructor(props) {
    super(props);
    this._pickImageFromCamera = this._pickImageFromCamera.bind(this);
    this._renderSingleImage = this._renderSingleImage.bind(this);
    this.removePicture = this.removePicture.bind(this);
    this._onPictureUploadedSuccessfully = this._onPictureUploadedSuccessfully.bind(
      this
    );
    this._onPictureUploadedFailed = this._onPictureUploadedFailed.bind(this);
  }

  state = { imagesArray: this.props.imagesArray };

  getUploadedImagesArray = () => {
    const uploadedImages = [];
    const stateImages = this.state.imagesArray;

    let uploadingImages = 0;

    // loop through images
    for (let i = 0; i < stateImages.length; i += 1) {
      if (stateImages[i].attachment) {
        uploadedImages.push(stateImages[i]);
      }
      if (stateImages[i].isLoading) {
        uploadingImages += 1;
      }
    }

    // check how many images are uploaded
    if (uploadedImages.length === 0) {
      this.addImagesError.setShowError(true);
    } else {
      this.addImagesError.setShowError(false);
    }

    // check if upload in progress
    if (uploadingImages > 0) {
      Util.alert(Strings.errorMessageUploadInProgress);
      return [];
    }

    return uploadedImages;
  };

  _pickImageFromCamera() {
    /*
    MediaPicker.showImagePicker(({ uri: file, path }) => {
      if (path) {
        this.setState({
          imagesArray: _.concat(this.state.imagesArray, {
            file,
            path,
            isLoading: true
          })
        });
      }
    });
    */

    MediaPicker.pickImageFromCamera(({ uri: file, path }) => {
      if (path) {
        this.setState({
          imagesArray: _.concat(this.state.imagesArray, {
            file,
            path,
            isLoading: true
          })
        });
      }
    });
  }

  removePicture = index => {
    const newDataImages = this.state.imagesArray
      ? _.clone(this.state.imagesArray)
      : [];
    newDataImages.splice(index, 1);
    this.setState({ imagesArray: newDataImages });
  };

  _onPictureUploadedSuccessfully = (index, attachment) => {
    this.state.imagesArray[index].attachment = attachment;
    this.state.imagesArray[index].isLoading = false;
    this.addImagesError.setShowError(false);
  };

  _onPictureUploadedFailed = index => {
    this.state.imagesArray[index].isLoading = false;
  };

  _renderTitle() {
    const { title } = this.props;
    return (
      <Text size="xxSmall" style={styles.title}>
        {title}
      </Text>
    );
  }

  _renderImagesInRow() {
    const { maxImages } = this.props;
    const { imagesArray } = this.state;
    return (
      <View style={styles.rowContainer}>
        {imagesArray.map(this._renderSingleImage)}
        {imagesArray.length < maxImages && this._renderAddView()}
      </View>
    );
  }

  _renderSingleImage(item, index) {
    return (
      <ItemImage
        index={index}
        file={item.file}
        key={index}
        attachment={item.attachment || undefined}
        onRemovePicture={this.removePicture}
        onUploadFailure={this._onPictureUploadedFailed}
        onUploadSuccess={this._onPictureUploadedSuccessfully}
      />
    );
  }

  _renderAddView() {
    return (
      <ButtonView
        style={styles.imageContainer}
        onPress={this._pickImageFromCamera}
      >
        <Image source={Images.addImage} />
      </ButtonView>
    );
  }

  render() {
    return (
      <View style={styles.container}>
        <View style={styles.imagesContainer}>
          {this._renderTitle()}
          {this._renderImagesInRow()}
        </View>
        <BoxError
          ref={ref => {
            this.addImagesError = ref;
          }}
        />
      </View>
    );
  }
}

export default AddImages;
