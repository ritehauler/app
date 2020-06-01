import React, { Component } from "react";
import { View, Image, TouchableOpacity } from "react-native";
import styles from "./styles";
import { Images, Colors, Metrics } from "../../theme";

export default class TabButtonLeft extends Component {
  state = {
    showIndicator: false
  };

  setShowIndicator(value) {
    this.setState({
      showIndicator: value
    });
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
        {this.state.showIndicator && (
          <View
            style={{
              position: "absolute",
              width: Metrics.ratio(12),
              height: Metrics.ratio(12),
              borderRadius: Metrics.ratio(6),
              backgroundColor: Colors.background.accent,
              marginRight: Metrics.ratio(4)
              //left: 0
            }}
          />
        )}
      </View>
    );
  }
}
