// @flow
import { StyleSheet } from "react-native";
import { Metrics, Colors } from "../../theme";

export default StyleSheet.create({
  modal: {
    margin: 0
  },
  body: {
    padding: Metrics.baseMargin * 1.5,
    margin: Metrics.baseMargin * 2,
    borderRadius: Metrics.smallMargin,
    backgroundColor: Colors.background.primary,
    alignItems: "center"
  },
  descriptionStyle: {
    marginBottom: Metrics.baseMargin * 1.5,
    textAlign: "center"
  },
  flexRow: {
    flexDirection: "row"
  },
  cancelButton: {
    flex: 1,
    borderRadius: Metrics.smallMargin / 2,
    borderWidth: 1,
    borderColor: Colors.background.black,
    marginRight: Metrics.baseMargin,
    alignItems: "center",
    justifyContent: "center"
  },
  gradientContainer: {
    flex: 1
  },
  gradientButton: {
    borderRadius: Metrics.smallMargin / 2,
    height: Metrics.baseMargin * 2.5
  }
});
