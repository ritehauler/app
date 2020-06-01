// @flow
import { StyleSheet } from "react-native";
import { Metrics } from "../../../theme";

export default StyleSheet.create({
  container: {
    flexDirection: "row"
  },
  image: {
    marginTop: Metrics.smallMargin * 0.5
  },
  text: {
    marginLeft: Metrics.baseMargin,
    lineHeight: Metrics.baseMargin * 1.4
  }
});
