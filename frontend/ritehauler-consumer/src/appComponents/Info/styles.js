// @flow
import { StyleSheet, Platform } from "react-native";
import { Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flexDirection: "column"
  },
  value: {
    marginTop:
      Platform.OS === "ios"
        ? Metrics.smallMargin * 0.5
        : Metrics.smallMargin * 0.1
  }
});
