// @flow
import { StyleSheet } from "react-native";
import { Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flexDirection: "row",
    marginRight: Metrics.baseMargin,
    marginLeft: Metrics.baseMargin * 0.7,
    marginTop: Metrics.smallMargin * 1.25,
    alignItems: "center",
    flex: 1
  },
  image: {
    marginRight: Metrics.smallMargin,
    width: Metrics.baseMargin * 2,
    height: Metrics.baseMargin * 2
  },
  customImagePlaceholderDefaultStyle: {
    width: Metrics.baseMargin * 2,
    height: Metrics.baseMargin * 2
  }
});
