// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    backgroundColor: Colors.background.primary
  },
  title: {
    marginTop: Metrics.baseMargin * 3,
    lineHeight: Metrics.baseMargin * 1.8,
    marginHorizontal: Metrics.baseMargin,
    textAlign: "center"
  },
  description: {
    marginTop: Metrics.smallMargin * 1.5,
    marginHorizontal: Metrics.baseMargin * 1.5,
    lineHeight: Metrics.baseMargin * 1.5,
    textAlign: "center"
  },
  buttonView: {
    paddingHorizontal: Metrics.baseMargin * 1.5,
    paddingVertical: Metrics.smallMargin * 1.5,
    borderColor: Colors.accent,
    borderWidth: 2,
    marginTop: Metrics.baseMargin * 1.5
  }
});
