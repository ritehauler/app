// @flow
import { StyleSheet, Platform } from "react-native";
import { Metrics } from "../../../theme";

export default StyleSheet.create({
  image: {
    width: Metrics.driverImage,
    height: Metrics.driverImage,
    marginLeft: Metrics.screenWidth / 2 - Metrics.driverImage / 2
  },
  name: {
    marginTop: Metrics.smallMargin,
    marginBottom: Metrics.baseMargin
  },
  statisticsContainer: {
    flexDirection: "row",
    justifyContent: "space-around",
    marginTop: Metrics.smallMargin / 2,
    marginBottom: Metrics.baseMargin
  },
  itemContainer: {
    alignItems: "center"
  },
  title: {
    marginBottom: Platform.OS === "ios" ? Metrics.smallMargin * 0.5 : 0
  },
  customImagePlaceholderDefaultStyle: {
    width: Metrics.driverImage,
    height: Metrics.driverImage
  }
});

/*
marginBottom: Platform.OS === "ios" ? Metrics.smallMargin / 2 : 1
*/
