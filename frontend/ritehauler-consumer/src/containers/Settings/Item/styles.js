// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../../theme";

export default StyleSheet.create({
  container: {
    backgroundColor: Colors.background.secondary,
    flexDirection: "row",
    padding: Metrics.baseMargin,
    alignItems: "center"
  }
});
