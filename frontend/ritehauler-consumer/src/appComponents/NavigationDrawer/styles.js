// @flow
import { StyleSheet, Platform } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  container: { flex: 1 },
  list: {
    flex: 1,
    backgroundColor: Colors.background.primary,
    marginTop: Platform.select({
      ios: 0,
      android: Platform.Version >= 19 ? Metrics.statusBarHeight : 0
    })
  },
  notification: {
    paddingTop: Metrics.baseMargin * 1.3
  }
});
