// @flow
import { StyleSheet } from "react-native";

import { Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    marginRight: Metrics.smallMargin,
    marginBottom: Metrics.smallMargin,
    marginTop: Metrics.baseMargin
  },
  text: {
    marginRight: Metrics.smallMargin,
    lineHeight: Metrics.baseMargin * 1.5,
    textAlign: "center"
  },
  image: {
    width: Metrics.baseMargin * 1.5,
    height: Metrics.baseMargin * 1.5
  }
});
