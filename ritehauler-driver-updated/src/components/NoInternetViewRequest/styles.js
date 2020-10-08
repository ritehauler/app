// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background.primary,
    alignItems: "center",
    justifyContent: "center"
  },
  leftButtonView: {
    borderWidth: Metrics.horizontalLineHeight,
    borderColor: Colors.black,
    borderRadius: Metrics.borderRadius,
    marginTop: Metrics.doubleBaseMargin * 2,
    paddingHorizontal: Metrics.doubleBaseMargin * 3,
    paddingVertical: Metrics.baseMargin
  },
  noInternetImage: {
    width: Metrics.image.large,
    height: Metrics.image.large,
    marginTop: -Metrics.doubleBaseMargin * 2
  },
  ops: {
    paddingTop: Metrics.baseMargin * 1.5,
    paddingHorizontal: Metrics.baseMargin,
    paddingBottom: Metrics.smallMargin
  },
  message: {
    paddingHorizontal: Metrics.baseMargin * 2
  }
});
