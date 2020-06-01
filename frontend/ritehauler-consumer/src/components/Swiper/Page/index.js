// @flow
import React, { Component } from "react";
import PropTypes from "prop-types";
import { View, Image } from "react-native";
import { Text } from "../../";

import styles from "./styles";

export default class Page extends Component {
  static propTypes = {
    data: PropTypes.object.isRequired,
    titleStyle: PropTypes.oneOfType([PropTypes.object, PropTypes.number]),
    imageStyle: PropTypes.oneOfType([PropTypes.object, PropTypes.number]),
    descriptionStyle: PropTypes.oneOfType([PropTypes.object, PropTypes.number])
  };
  static defaultProps = {
    titleStyle: styles.title,
    imageStyle: styles.image,
    descriptionStyle: styles.description
  };

  state = {
    components: undefined
  };

  _renderTitle({ title, textColor }, titleStyle) {
    return (
      title &&
      <Text
        key="title"
        size="large"
        color={textColor || "primary"}
        style={titleStyle}
      >
        {title}
      </Text>
    );
  }

  _renderImage({ imageSource }, imageStyle) {
    return (
      imageSource &&
      <Image
        key="image"
        resizeMode="contain"
        source={imageSource}
        style={imageStyle}
      />
    );
  }

  _renderDescription({ description, textColor }, descriptionStyle) {
    return (
      description &&
      <Text
        size="large"
        key="description"
        color={textColor || "primary"}
        style={descriptionStyle}
      >
        {description}
      </Text>
    );
  }

  componentDidMount() {
    const { data, titleStyle, imageStyle, descriptionStyle } = this.props;

    const components = [];

    Object.keys(data).forEach(key => {
      if (key === "title") {
        components.push(this._renderTitle(data, titleStyle));
      } else if (key === "imageSource") {
        components.push(this._renderImage(data, imageStyle));
      } else if (key === "description") {
        components.push(this._renderDescription(data, descriptionStyle));
      }
    });

    this.setState({
      components
    });
  }

  render() {
    const { backgroundColor } = this.props.data;
    return (
      <View style={[styles.container, { backgroundColor }]}>
        {this.state.components}
      </View>
    );
  }
}
