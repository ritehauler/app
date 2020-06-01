// @flow
import { StyleSheet } from "react-native";
import { Metrics, Colors } from "../../theme";

export default StyleSheet.create({
  container: {
    marginTop: Metrics.smallMargin * 1.25
  },
  errorContainer: {
    marginBottom: Metrics.smallMargin / 2
  },
  line: {
    backgroundColor: Colors.background.error,
    height: 1
  },
  errorMessage: {
    marginTop: Metrics.smallMargin / 2
  }
});
