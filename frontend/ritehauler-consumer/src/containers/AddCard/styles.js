// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background.primary,
    padding: Metrics.baseMargin
  },
  cardContainer: {
    backgroundColor: Colors.background.secondary,
    paddingVertical: Metrics.smallMargin * 0.5
  },
  field: {
    width: Metrics.screenWidth - Metrics.baseMargin * 2,
    color: Colors.text.primary,
    borderColor: Colors.background.secondary,
    borderWidth: 0,
    borderRadius: 0,
    backgroundColor: "transparent"
  }
});
