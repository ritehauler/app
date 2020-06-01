// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background.primary
  },
  scroll: {
    flex: 1
  },
  contentContainerStyle: {
    padding: Metrics.baseMargin
  },
  input: {
    paddingVertical: Metrics.smallMargin / 2
  }
});
