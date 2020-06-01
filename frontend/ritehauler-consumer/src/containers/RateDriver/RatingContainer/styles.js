// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../../theme";

export default StyleSheet.create({
  container: {
    paddingVertical: Metrics.baseMargin * 1.5,
    backgroundColor: Colors.background.secondary,
    alignItems: "center"
  }
});
