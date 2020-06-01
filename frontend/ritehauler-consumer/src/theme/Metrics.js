/*
 * @flow
 * TODO: value * ratio difference between Android and iOS is of 2 value;
 * 16 in iOS is equals to 14 in android but this need to be verify.
 */

import { Dimensions, Platform } from "react-native";

const { width, height } = Dimensions.get("window");

const screenWidth = width < height ? width : height;
const screenHeight = width < height ? height : width;

const isIphoneX = height >= 812 && Platform.OS === "ios";

const isKitKatAbove = Platform.OS === "android" && Platform.Version >= 19;

const guidelineBaseWidth = isIphoneX ? 375 : 414;
const guidelineBaseHeight = isIphoneX ? 812 : 736;

const scale = size => (screenWidth / guidelineBaseWidth) * +size;
const scaleVertical = size => (screenHeight / guidelineBaseHeight) * size;

const ratio = (iosSize: number, androidSize: ?number) =>
  Platform.select({
    ios: iosSize,
    android: androidSize || iosSize
  });

const generatedFontSize = (iosFontSize: number, androidFontSize: ?number) =>
  Platform.select({
    ios: iosFontSize,
    android: androidFontSize || iosFontSize
  });

//ios: scale(iosFontSize),
//ios: scaleVertical(iosSize),

const NAVBAR_HEIGHT = Platform.OS === "ios" ? 44 : 56;
const STATUSBAR_HEIGHT = Platform.OS === "ios" ? (isIphoneX ? 44 : 20) : 24;
const LIST_BOTTOM_PADDING = isIphoneX ? ratio(40) : ratio(16);

export default {
  ratio,
  scaleVertical,
  screenWidth,
  screenHeight,
  generatedFontSize,
  isIphoneX,
  isKitKatAbove,
  smallMargin: ratio(8),
  baseMargin: ratio(16),
  inputSpacing: ratio(5.6),
  addItemInputSpacing: ratio(1),
  listBottomPadding: LIST_BOTTOM_PADDING,
  bottomSpaceIphoneX: ratio(40),
  doubleBaseMargin: ratio(32),
  statusBarHeight: STATUSBAR_HEIGHT,
  horizontalLineHeight: ratio(1),
  navBarHeight: NAVBAR_HEIGHT + STATUSBAR_HEIGHT,
  navBarHeightWithoutStatus: NAVBAR_HEIGHT,
  tabBarHeight: 49, // Default tab bar height in iOS 10 (source react-navigation)
  borderRadius: ratio(4),
  defaultUIHeight: ratio(47),
  buttonUIHeight: ratio(54),
  uploadImage: screenWidth / 4,
  profileImage: ratio(120),
  driverImage: ratio(90),
  statisticsImage: ratio(60),
  separatorHeight: ratio(1)
};

/*
addItemInputSpacing: ratio(3.2),
*/
