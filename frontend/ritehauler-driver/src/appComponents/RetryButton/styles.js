// @flow
import { StyleSheet } from "react-native";
import { Metrics, Colors } from "../../theme";

export default StyleSheet.create({
  linearGradient: {
    height: Metrics.buttonUIHeight,
    padding: Metrics.ratio(2),
    justifyContent: "center",
    alignItems: "center"
  }
});
