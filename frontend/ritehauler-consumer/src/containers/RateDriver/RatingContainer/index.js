// @flow
import React from "react";
import { View } from "react-native";

import { Metrics } from "../../../theme";
import styles from "./styles";
import { Rating } from "../../../components";

export default class RatingContainer extends React.PureComponent {
  constructor(props) {
    super(props);
    this.onStarRatingPress = this.onStarRatingPress.bind(this);
  }

  state = {
    starCount: 0
  };

  onStarRatingPress(rating) {
    this.setState({
      starCount: rating
    });
  }

  getRating = () => this.state.starCount;

  render() {
    return (
      <View style={styles.container}>
        <Rating
          starSize={32}
          rating={this.state.starCount}
          selectedStar={this.onStarRatingPress}
          starStyle={{ marginRight: Metrics.smallMargin * 1.5 }}
        />
      </View>
    );
  }
}
