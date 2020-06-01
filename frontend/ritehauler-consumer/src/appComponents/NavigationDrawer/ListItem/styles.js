// @flow
import { StyleSheet, Platform } from "react-native";

import { Metrics } from "../../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    flexDirection: "row",
    paddingLeft: Metrics.baseMargin,
    paddingRight: Metrics.baseMargin,
    paddingVertical: Metrics.baseMargin,
    alignItems: "center"
  },
  title: {
    flex: 1,
    marginRight: Metrics.smallMargin
  },
  rightImage: {
    marginLeft: Metrics.smallMargin * 1.2
  }
});
