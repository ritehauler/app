// @flow
import { StyleSheet } from "react-native";
import { Metrics, Colors } from "../../../theme";

export default StyleSheet.create({
  container: {
    marginHorizontal: Metrics.baseMargin,
    marginVertical: Metrics.listBottomPadding,
    paddingHorizontal: Metrics.baseMargin,
    paddingVertical: Metrics.baseMargin * 1.5,
    backgroundColor: Colors.background.secondary,
    alignItems: "center"
  }
});
