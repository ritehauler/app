// @flow
import { StyleSheet } from "react-native";
import { Metrics } from "../../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    flexDirection: "row",
    paddingHorizontal: Metrics.baseMargin,
    paddingVertical: Metrics.baseMargin,
    alignItems: "center"
  },
  title: {
    flex: 1
  }
});
