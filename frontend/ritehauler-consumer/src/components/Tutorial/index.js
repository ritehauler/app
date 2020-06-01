// @flow
import { connect } from "react-redux";
import PropTypes from "prop-types";
import React, { Component } from "react";
import { View, Button } from "react-native";
import styles from "./styles";
import { Images } from "../../theme";

import { Swiper } from "../../components";

const data = [
  {
    imageSource: Images.splash1,
    title: "Aute sint cupidatat tempor minim.",
    description:
      "Do velit aliqua in eu. Irure irure nulla cupidatat tempor deserunt dolor aliquip commodo sit. Sunt magna elit mollit exercitation. Labore anim deserunt do do. Reprehenderit mollit cillum Lorem sunt fugiat ipsum."
  },
  {
    title: "Aute sint cupidatat tempor minim.",
    imageSource: {
      uri:
        "https://61978.apps.zdusercontent.com/61978/assets/1481841069-b3f33f8b4f2e144a10a72ee1b97372d7/logo.png"
    },
    description:
      "Do velit aliqua in eu. Irure irure nulla cupidatat tempor deserunt dolor aliquip commodo sit. Sunt magna elit mollit exercitation. Labore anim deserunt do do. Reprehenderit mollit cillum Lorem sunt fugiat ipsum."
  },
  {
    title: "Aute sint cupidatat tempor minim.",
    imageSource: {
      uri:
        "https://61978.apps.zdusercontent.com/61978/assets/1481841069-b3f33f8b4f2e144a10a72ee1b97372d7/logo.png"
    },
    description:
      "Do velit aliqua in eu. Irure irure nulla cupidatat tempor deserunt dolor aliquip commodo sit. Sunt magna elit mollit exercitation. Labore anim deserunt do do. Reprehenderit mollit cillum Lorem sunt fugiat ipsum."
  }
];

class Tutorial extends Component {
  static propTypes = {
    navigation: PropTypes.object.isRequired
  };

  static navigationOptions = {
    title: "Tutorial",
    header: null
  };

  state = {
    index: 0
  };

  render() {
    return (
      <View style={styles.container}>
        <Swiper
          data={data}
          onMomentumScrollEnd={(e, { index }) => this.setState({ index })}
        />
        {this.state.index === data.length - 1 &&
          <View style={styles.footer}>
            <Button
              title="Next Screen"
              onPress={() => this.props.navigation.goBack()}
            />
          </View>}
      </View>
    );
  }
}

export default connect()(Tutorial);
