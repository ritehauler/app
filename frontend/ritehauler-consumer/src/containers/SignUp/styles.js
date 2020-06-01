// @flow
import { StyleSheet, Platform } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  scroll: {
    flex: 1
  },
  container: {
    flex: 1,
    backgroundColor: Colors.background.secondary
  },
  contentContainerStyle: {
    paddingHorizontal: Metrics.baseMargin,
    paddingBottom: Metrics.baseMargin,
    paddingTop: Platform.OS === "ios" ? Metrics.smallMargin * 0.75 : 0
  },
  firstName: {
    flex: 1
  },
  lastName: {
    flex: 1,
    marginLeft: Metrics.inputSpacing * 2
  },
  name: {
    flexDirection: "row"
  },
  signUpButton: {
    marginTop: Metrics.smallMargin * 0.5
  },
  confirmView: {
    marginHorizontal: Metrics.screenWidth / 24,
    lineHeight: 22,
    marginTop: Metrics.smallMargin
  },
  links: {
    flexDirection: "row",
    justifyContent: "center",
    marginTop:
      Platform.OS === "ios"
        ? Metrics.smallMargin * 0.75
        : Metrics.smallMargin * 0.5
  }
});
