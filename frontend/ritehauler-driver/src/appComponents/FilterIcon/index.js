import React, { Component } from "react";
import { View, Image, TouchableOpacity, StyleSheet, Text } from "react-native";
import LinearGradient from "react-native-linear-gradient";
import { Images, Colors, Metrics, ApplicationStyles } from "../../theme";
import { GRADIENT_START, GRADIENT_END } from "../../constant";
export default class TabButtonLeft extends Component {
  state = {
    showIndicator: false,
    activeFilters: 0
  };

  setShowIndicator(value, count) {
    this.setState({
      showIndicator: value,
      activeFilters: count
    });
  }

  renderIndicator() {
    return this.state.showIndicator ? (
      <LinearGradient
        colors={Colors.lgColArray}
        style={[styles.linearGradient]}
        start={GRADIENT_START}
        end={GRADIENT_END}
      >
        <Text style={ApplicationStyles.re16White}>
          {this.state.activeFilters}
        </Text>
      </LinearGradient>
    ) : null;
  }

  render() {
    return (
      <View style={styles.container}>
        {this.props.imagesArray.map((item, index) => (
          <TouchableOpacity
            key={index}
            style={styles.iconContainer}
            onPress={() =>
              this.props.actions[index] && this.props.actions[index]()
            }
          >
            <Image source={Images[item]} />
          </TouchableOpacity>
        ))}
        {this.renderIndicator()}
      </View>
    );
  }
}

const styles = StyleSheet.create({
  container: {
    flexDirection: "row",
    height: Metrics.navBarHeight,
    alignItems: "center",
    justifyContent: "center"
  },
  iconContainer: {
    paddingHorizontal: Metrics.baseMargin,
    alignItems: "center",
    justifyContent: "center",
    height: Metrics.navBarHeight - Metrics.statusBarHeight
  },
  icon: {
    width: Metrics.ratio(17),
    height: Metrics.ratio(20)
  },
  linearGradient: {
    position: "absolute",
    width: Metrics.ratio(24),
    height: Metrics.ratio(24),
    borderRadius: Metrics.ratio(12),
    backgroundColor: Colors.background.accent,
    marginRight: Metrics.ratio(4),
    left: 0,
    top: Metrics.smallMargin * 1.3,
    alignItems: "center",
    justifyContent: "center"
  }
});
