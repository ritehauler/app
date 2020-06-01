// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics, Fonts } from "../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background.primary
  },
  mapContainer: {
    flex: 1
  },
  box: {
    position: "absolute",
    backgroundColor: Colors.background.secondary,
    margin: Metrics.baseMargin,
    left: 0,
    right: 0
  },
  currentLocation: {
    position: "absolute",
    right: 12,
    bottom: 12
  },
  title: {
    fontSize: Fonts.size.large,
    fontFamily: Fonts.type.bold2,
    color: Colors.text.primary
  }
});
