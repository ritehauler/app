// @flow
import ImagePicker from "react-native-image-picker";
//import ImageResizer from "react-native-image-resizer";
//import MultiImagePicker from "react-native-image-crop-picker";
//import Permissions from "react-native-permissions";
// import { ProcessingManager } from "react-native-video-processing";
//import OpenSettings from "react-native-open-settings";
import { Alert } from "react-native";
import Utils from "../util";

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

const LOG = __DEV__ && false;

class MediaPicker {
  resizeImage(uri, cb) {
    ImageResizer.createResizedImage(
      uri,
      IMAGE_COMPRESS_MAX_WIDTH,
      IMAGE_COMPRESS_MAX_HEIGHT,
      IMAGE_COMPRESS_FORMAT,
      100
    ).then(response => {
      if (LOG) {
        console.log("ImageResizer.createResizedImage", response);
      }

      if (cb) {
        cb(response);
      }
    });
  }

  showImagePicker(cb, options = IMAGE_PICKER_OPTIONS) {
    Permissions.checkMultiple(["camera", "photo"]).then(response => {
      if (response.camera === "authorized" && response.photo === "authorized") {
        this.openShowImagePicker(options, cb);
      } else {
        this.checkPermissions(cb, "showImagePicker", options);
      }
    });
  }

  checkPermissions(cb, runOnceAuthorized, options) {
    Permissions.checkMultiple(["camera", "photo"]).then(response => {
      let camera_request_required = false;
      let photo_request_required = false;
      let open_setting_required = false;

      camera_request_required =
        response.camera === "undetermined" || response.camera === "denied";

      photo_request_required =
        response.photo === "undetermined" || response.photo === "denied";

      open_setting_required =
        response.camera === "restricted" || response.photo === "restricted";

      if (open_setting_required) {
        this.openSettingModal();
        if (runOnceAuthorized === "imagePicker") {
          cb({
            uri: null
          });
        } else if (runOnceAuthorized === "videoPicker") {
          // ok
          cb({
            videoPath: null,
            thumbPath: null
          });
        } else {
          cb(null, null);
        }
      } else if (!camera_request_required && !photo_request_required) {
        this.onPermissionGranted(cb, runOnceAuthorized, options);
      } else if (camera_request_required) {
        Permissions.request("camera", { type: "always" }).then(
          cameraResponse => {
            if (cameraResponse === "authorized") {
              Permissions.request("photo", { type: "always" }).then(
                photoResponse => {
                  if (photoResponse === "authorized") {
                    this.onPermissionGranted(cb, runOnceAuthorized, options);
                  }
                }
              );
            } else if (Utils.isPlatformIOS()) {
              this.openSettingModal();
            }
          }
        );
      } else if (photo_request_required) {
        Permissions.request("photo", { type: "always" }).then(photoResponse => {
          if (photoResponse === "authorized") {
            this.onPermissionGranted(cb, runOnceAuthorized, options);
          } else if (Utils.isPlatformIOS()) {
            this.openSettingModal();
          }
        });
      }
    });
  }

  onPermissionGranted(cb, runOnceAuthorized, options) {
    if (runOnceAuthorized === "imagePicker") {
      // authorized
      this.openImagePicker(options, cb);
    } else if (runOnceAuthorized === "videoPicker") {
      // authorized
      this.openVideoPicker(options, cb);
    } else if (runOnceAuthorized === "showImagePicker") {
      // authorized
      this.openShowImagePicker(options, cb);
    } else if (runOnceAuthorized === "pickVideoFromCamera") {
      // authorized
      this.openPickVideoFromCamera(options, cb);
    } else if (runOnceAuthorized === "pickImageFromCamera") {
      // authorized
      this.openPickImageFromCamera(options, cb);
    } else if (runOnceAuthorized === "pickImageFromGallery") {
      // authorized
      this.openImagePicker(options, cb);
    }
  }

  openSettingModal() {
    Alert.alert(
      "Permission required",
      "Need permissions to access gallery and camera",
      [
        {
          text: "Cancel",
          style: "cancel"
        },
        {
          text: "Open Settings",
          onPress: () => OpenSettings.openSettings()
        }
      ],
      { cancelable: false }
    );
  }

  openImagePicker(crop, cb) {
    MultiImagePicker.openPicker({
      multiple: false,
      mediaType: "photo",
      cropping: crop,
      height: 300,
      width: 300
    }).then(image => {
      if (cb) {
        this.resizeImage(image.path, cb);
      }
    });
  }

  openShowImagePicker(options, cb) {
    ImagePicker.showImagePicker(options, response => {
      if (LOG) {
        console.log(response);
      }

      if (!response.didCancel && !response.error) {
        if (cb) {
          this.resizeImage(response.uri, cb);
        }
      }
    });
  }

  openPickImageFromCamera(options, cb) {
    ImagePicker.launchCamera(options, response => {
      if (LOG) {
        console.log("ImagePicker.launchCamera", response);
      }

      if (!response.didCancel && !response.error) {
        if (cb) {
          this.resizeImage(response.uri, cb);
        }
      }
    });
  }

  async pickImageFromGallery(cb, crop) {
    Permissions.checkMultiple(["camera", "photo"]).then(response => {
      if (response.camera === "authorized" && response.photo === "authorized") {
        this.openImagePicker(crop, cb);
      } else {
        this.checkPermissions(cb, "pickImageFromGallery", crop);
      }
    });
  }

  async pickImageFromCamera(cb, options = IMAGE_PICKER_OPTIONS) {
    // Permissions.checkMultiple(["camera", "photo"]).then(response => {
    //   if (response.camera === "authorized" && response.photo === "authorized") {
    this.openPickImageFromCamera(options, cb);
    //   } else {
    //     this.checkPermissions(cb, "pickImageFromCamera", options);
    //   }
    // });
  }
}

export default new MediaPicker();
