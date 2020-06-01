// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../../theme";

export default StyleSheet.create({
  container: {
    backgroundColor: Colors.background.secondary
  },
  title: {
    flex: 1,
    marginRight: Metrics.smallMargin,
    padding: 16
  },
  separator: {
    marginHorizontal: Metrics.baseMargin
  }
});
