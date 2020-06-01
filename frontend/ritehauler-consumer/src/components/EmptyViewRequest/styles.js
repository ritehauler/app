// @flow
import { StyleSheet } from "react-native";

import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: Colors.background.primary
  },
  message: {
    textAlign: "center",
    lineHeight: Metrics.baseMargin * 1.5
  }
});
