// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../../theme";

export default StyleSheet.create({
  container: {
    backgroundColor: Colors.background.secondary
  },
  viewAllContainer: {
    flexDirection: "row",
    padding: Metrics.smallMargin * 1.5,
    alignItems: "center"
  },
  viewAllText: {
    flex: 1
  },
  separator: {
    marginHorizontal: Metrics.baseMargin
  }
});
