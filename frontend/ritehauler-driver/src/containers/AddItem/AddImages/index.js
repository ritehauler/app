// @flow
import _ from "lodash";
import React from "react";
import { View, Image } from "react-native";
import PropTypes from "prop-types";
import ImagePicker from "react-native-image-picker";
import ImageResizer from "react-native-image-resizer";
import { Images } from "../../../theme";
import styles from "./styles";
import { Text, ButtonView } from "../../../components";
import IP from "../../../util/ImagePicker";
import { BoxError } from "../../../appComponents";
import ItemImage from "../ItemImage";

import {
  IMAGE_QUALITY,
  IMAGE_MAX_WIDTH,
  IMAGE_MAX_HEIGHT,
  IMAGE_COMPRESS_MAX_WIDTH,
  IMAGE_COMPRESS_MAX_HEIGHT,
  IMAGE_COMPRESS_FORMAT
} from "../../../constant";

export default class AddImages extends React.PureComponent {
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
      Util.showMessage(Strings.errorMessageUploadInProgress);
      return [];
    }

    return uploadedImages;
  };

  resizeImage = imageUri => {
    ImageResizer.createResizedImage(
      imageUri,
      IMAGE_COMPRESS_MAX_WIDTH,
      IMAGE_COMPRESS_MAX_HEIGHT,
      IMAGE_COMPRESS_FORMAT,
      100
    )
      .then(resized => {
        if (resized.uri) {
          let tempArray = _.cloneDeep(this.state.imagesArray);
          tempArray.push({ file: resized.uri });
          this.setState({
            imagesArray: tempArray
          });
        }
      })
      .catch(err => {});
  };

  _pickImageFromCamera() {
    // ImagePicker.showImagePicker(IP.IMAGE_PICKER_OPTIONS, response => {
    //   if (response.uri) {
    //     this.resizeImage(response.uri);
    //   }
    // });
    ImagePicker.launchCamera(IP.IMAGE_PICKER_OPTIONS, response => {
      if (response.uri) {
        this.resizeImage(response.uri);
      }
    });
  }

  _onPictureUploadedSuccessfully = (index, attachment) => {
    this.state.imagesArray[index].attachment = attachment;
    this.state.imagesArray[index].isLoading = false;
    this.addImagesError.setShowError(false);
  };

  _onPictureUploadedFailed = index => {
    this.state.imagesArray[index].isLoading = false;
  };

  _removePicture = index => {
    const newDataImages = this.state.imagesArray
      ? _.clone(this.state.imagesArray)
      : [];
    newDataImages.splice(index, 1);
    this.setState({ imagesArray: newDataImages });
  };

  _renderTitle() {
    const { title } = this.props;
    return <Text style={styles.title}>{title}</Text>;
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
        onRemovePicture={this._removePicture}
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
          errorMessage="item image required"
        />
      </View>
    );
  }
}
