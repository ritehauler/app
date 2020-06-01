// @flow
import React from "react";
import { ScrollView, StyleSheet, Image, Text, View } from "react-native";
import { RetryButton } from "../../appComponents";
import { Colors, Images, ApplicationStyles, Metrics } from "../../theme";

const EmptyList = ({ title, image, cbOnRetry, description }) => {
  return (
    <ScrollView>
      <View style={styles.container}>
        <Image source={image || Images.empty} />
        <View style={styles.textWrapper}>
          <Text style={[ApplicationStyles.dBoldB20, styles.doubleTopPadding]}>
            {title || "You have no orders"}
          </Text>
          <Text style={[ApplicationStyles.re15Gray, styles.topPadding]}>
            {description ||
              "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s"}
          </Text>
        </View>
        {cbOnRetry && <RetryButton text="Retry" onPress={() => cbOnRetry()} />}
      </View>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background.login,
    alignItems: "center",
    paddingVertical: Metrics.doubleBaseMargin
  },
  textWrapper: {
    alignItems: "center",
    justifyContent: "center",
    padding: Metrics.baseMargin,
    paddingBottom: Metrics.doubleBaseMargin
  },
  topPadding: {
    textAlign: "center",
    paddingTop: Metrics.baseMargin
  },
  doubleTopPadding: {
    textAlign: "center",
    paddingTop: Metrics.doubleBaseMargin * 1.5
  },
  buttonWrapper: {}
});

export default EmptyList;
