// @flow
import { StyleSheet } from "react-native";

import { Metrics, Colors } from "../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: Colors.background.primary
  },
  text: {
    paddingHorizontal: Metrics.baseMargin,
    lineHeight: Metrics.baseMargin * 1.4,
    textAlign: "center"
  },
  buttonRetry: {
    marginTop: Metrics.baseMargin * 1.5
  },
  styleGradient: {
    paddingVertical: 0,
    paddingHorizontal: Metrics.baseMargin
  }
});
