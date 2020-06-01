// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../../theme";

export default StyleSheet.create({
  container: {
    marginHorizontal: Metrics.baseMargin
  },
  weight: {
    marginTop: Metrics.smallMargin,
    marginBottom: Metrics.baseMargin
  },
  imageContainer: {
    alignItems: "center",
    justifyContent: "center",
    marginVertical: Metrics.baseMargin
  },
  contentContainerStyle: {
    padding: Metrics.baseMargin,
    marginTop: Metrics.smallMargin,
    backgroundColor: Colors.background.secondary
  },
  costContainer: {
    flexDirection: "row",
    marginTop: Metrics.baseMargin
  },
  costLabel: {
    flex: 1
  },
  image: {
    width: "100%",
    height: Metrics.ratio(150)
  }
});
