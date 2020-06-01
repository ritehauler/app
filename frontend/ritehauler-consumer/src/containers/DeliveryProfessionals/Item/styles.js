// @flow
import { StyleSheet } from "react-native";
import { Metrics } from "../../../theme";

export default StyleSheet.create({
  imageContainer: {
    flexDirection: "row",
    alignItems: "center",
    marginVertical: Metrics.baseMargin * 2
  },
  imageBox: {
    flex: 1,
    alignItems: "center",
    marginHorizontal: Metrics.smallMargin * 1.5
  },
  image: {
    width: "100%",
    height: Metrics.ratio(150)
  },
  info: {
    marginTop: Metrics.baseMargin,
    lineHeight: Metrics.baseMargin * 1.2,
    marginBottom: Metrics.baseMargin
  },
  disable: {
    opacity: 0.5
  }
});
