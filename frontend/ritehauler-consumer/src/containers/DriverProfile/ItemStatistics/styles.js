// @flow
import { StyleSheet } from "react-native";
import { Metrics, Colors } from "../../../theme";

export default StyleSheet.create({
  container: {
    marginRight: Metrics.baseMargin * 1.5
  },
  text: {
    marginTop: Metrics.smallMargin,
    width: Metrics.baseMargin * 4.5,
    marginLeft: Metrics.smallMargin * -0.25
  },
  badge: {
    position: "absolute",
    right: 0,
    backgroundColor: Colors.background.accent,
    height: Metrics.baseMargin * 1.5,
    width: Metrics.baseMargin * 1.5,
    borderRadius: (Metrics.baseMargin * 1.5) / 2,
    alignItems: "center",
    justifyContent: "center",
    borderWidth: Metrics.smallMargin * 0.2,
    borderColor: Colors.background.secondary
  },
  image: {
    marginLeft: Metrics.smallMargin * 0.75,
    width: Metrics.statisticsImage,
    height: Metrics.statisticsImage
  },
  customImagePlaceholderDefaultStyle: {
    width: Metrics.baseMargin * 1.2,
    height: Metrics.baseMargin * 1.2
  }
});
