// @flow
import { StyleSheet, Platform } from "react-native";
import { Colors, Metrics } from "../../../theme";

export default StyleSheet.create({
  content: {
    paddingHorizontal: Metrics.smallMargin * 1.5,
    paddingTop:
      Platform.OS === "ios" ? Metrics.smallMargin * 1.3 : Metrics.smallMargin,
    paddingBottom: Metrics.smallMargin * 1.5,
    flex: 1,
    borderWidth: 1,
    borderColor: Colors.background.separator
  },
  label: {
    marginTop: Metrics.smallMargin * 0.5
  },
  container: { flex: 1, height: Metrics.baseMargin * 4 }
});
