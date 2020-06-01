// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../../theme";

export default StyleSheet.create({
  container: {
    backgroundColor: Colors.background.secondary
  },
  content: {
    flexDirection: "row",
    paddingHorizontal: Metrics.baseMargin,
    paddingVertical: Metrics.baseMargin,
    alignItems: "center"
  },
  separator: {
    marginHorizontal: Metrics.baseMargin
  },
  itemNames: {
    flex: 1
  }
});
