// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics, Fonts } from "../../theme";

export default StyleSheet.create({
  container: {
    padding: Metrics.baseMargin,
    backgroundColor: Colors.background.primary,
    marginHorizontal: Metrics.baseMargin,
    marginBottom: Metrics.baseMargin
  },
  separator: {
    height: 1,
    backgroundColor: Colors.background.login
  },
  title: {
    marginBottom: Metrics.baseMargin
  },
  textInput: {
    paddingBottom: 0,
    paddingTop: Metrics.baseMargin
  }
});
