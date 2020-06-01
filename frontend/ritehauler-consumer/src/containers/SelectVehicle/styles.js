// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background.primary
  },
  scrollTabs: {
    flex: 1,
    marginTop: Metrics.baseMargin,
    marginBottom: Metrics.smallMargin / 2
  },
  tabBarUnderlineStyle: {
    backgroundColor: Colors.background.accent,
    height: Metrics.smallMargin / 3.2
  },
  tabStyle: {
    marginVertical: Metrics.baseMargin
  },
  defaultTabBarStyle: {
    marginHorizontal: Metrics.baseMargin,
    backgroundColor: Colors.background.secondary,
    height: Metrics.baseMargin * 5,
    marginBottom: Metrics.smallMargin / 2
  },
  tabView: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
    paddingBottom: Metrics.smallMargin
  },
  tabImage: {
    width: 70,
    height: 50
  }
});
