// @flow
import { StyleSheet } from "react-native";
import { Metrics } from "../../../theme";

export default StyleSheet.create({
  container: {
    marginHorizontal: Metrics.baseMargin
  },
  noteContainer: {
    padding: Metrics.baseMargin,
    marginVertical: Metrics.baseMargin,
    marginHorizontal: 0
  },
  errorContainer: {
    marginBottom: 0
  },
  containerStyle: { marginTop: 0 }
});
