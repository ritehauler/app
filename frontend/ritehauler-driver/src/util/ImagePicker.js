import ImagePicker from "react-native-image-picker";

import {
  IMAGE_QUALITY,
  IMAGE_MAX_WIDTH,
  IMAGE_MAX_HEIGHT,
  IMAGE_COMPRESS_MAX_WIDTH,
  IMAGE_COMPRESS_MAX_HEIGHT,
  IMAGE_COMPRESS_FORMAT
} from "../constant";

const IMAGE_PICKER_OPTIONS = {
  //mediaType: "mixed",
  quality: IMAGE_QUALITY,
  maxWidth: IMAGE_MAX_WIDTH,
  maxHeight: IMAGE_MAX_HEIGHT,
  title: "Select Image",
  cancelButtonTitle: "cancel",
  takePhotoButtonTitle: "Camera",
  allowsEditing: true,
  chooseFromLibraryButtonTitle: "Gallery",
  mediaType: "photo",
  permissionDenied: {
    title: "Permission Denied"
  }
};

const getImage = () => {
  ImagePicker.showImagePicker(IMAGE_PICKER_OPTIONS, response => {
    return response.uri;
  });
};

export default {
  IMAGE_PICKER_OPTIONS
};
