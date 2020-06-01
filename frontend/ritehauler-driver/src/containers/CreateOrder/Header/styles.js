// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../../theme";

export default StyleSheet.create({
  container: {
    marginTop: Metrics.baseMargin,
    backgroundColor: Colors.background.primary
  },
  separator: {
    height: Metrics.baseMargin,
    backgroundColor: Colors.background.login
  },
  content: {
    flexDirection: "row",
    paddingHorizontal: Metrics.baseMargin,
    paddingVertical: Metrics.baseMargin,
    alignItems: "center",
    flex: 1
  },
  itemNames: {
    flex: 1
  }
});
