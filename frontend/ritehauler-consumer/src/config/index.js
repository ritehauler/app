// @flow
import { Text, StatusBar, YellowBox } from "react-native";

import IQKeyboardManager from "./IQKeyboardManager";
import DebugSettings from "./DebugSettings";
import AppConfig from "./AppConfig";
import { Colors } from "../theme";
import Utils from "../util";

export default () => {
  if (__DEV__) {
    /*
    YellowBox.ignoreWarnings([
      "Warning: isMounted(...) is deprecated",
      "Module RCTImageLoader"
    ]);
    */
    // eslint-disable-next-line no-console
    console.disableYellowBox = !DebugSettings.yellowBox;
  }

  // Allow/disallow IQKeyboardManager in app
  IQKeyboardManager.setEnable(AppConfig.allowIQKeyboardManager);
  IQKeyboardManager.setToolbarPreviousNextButtonEnable(
    AppConfig.allowIQKeyboardManagerToolbar
  );

  if (Utils.isPlatformAndroid()) {
    StatusBar.setBackgroundColor(Colors.background.statusBarTranslucent);
    StatusBar.setTranslucent(true);
  }

  // Allow/disallow font-scaling in app
  //Text.defaultProps.allowFontScaling = AppConfig.allowTextFontScaling;
};
