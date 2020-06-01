// @flow
import { StyleSheet } from "react-native";
import { Metrics, Colors } from "../../../theme";

export default StyleSheet.create({
  container: {
    //marginHorizontal: Metrics.baseMargin
  },
  separator: {
    height: Metrics.baseMargin,
    backgroundColor: Colors.background.login
  },
  noteContainer: {
    marginHorizontal: 0
  },
  errorContainer: {
    marginBottom: 0
  },
  containerStyle: { marginTop: 0 }
});
